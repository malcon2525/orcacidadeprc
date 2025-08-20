import PyPDF2
import tabula
import pandas as pd
import json
import sys
import os
import re
from datetime import datetime

# Configurar codificação para UTF-8
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

def log_to_file(message, origem='PYTHON'):
    log_file = os.path.abspath(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../..', 'storage', 'logs', 'importacao_tabelas_oficiais.log')))
    os.makedirs(os.path.dirname(log_file), exist_ok=True)
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    with open(log_file, 'a', encoding='utf-8') as f:
        f.write(f'[{timestamp}] [{origem}] {message}\n')

def extrair_desoneracao(texto):
    """Extrai o tipo de desoneração do texto do PDF"""
    if "sem desoneração" in texto.lower() or "sem bonificação" in texto.lower():
        return "sem"
    elif "com desoneração" in texto.lower():
        return "com"
    return "sem"  # valor padrão

def extrair_data_base(texto):
    """Extrai a data base do texto do PDF"""
    # Padrão: "Data Base: 31/03/2025"
    match = re.search(r'Data Base:\s*(\d{2}/\d{2}/\d{4})', texto, re.IGNORECASE)
    if match:
        data_str = match.group(1)
        try:
            # Converter para formato YYYY-MM-DD
            data_obj = datetime.strptime(data_str, '%d/%m/%Y')
            return data_obj.strftime('%Y-%m-%d')
        except:
            return None
    return None

def processar_pdf(caminho_arquivo):
    """Processa o PDF de fórmulas de transporte e extrai os dados"""
    try:
        if not os.path.exists(caminho_arquivo):
            return {"error": f'Arquivo não encontrado: {caminho_arquivo}'}
        
        log_to_file(f'Iniciando processamento do PDF: {caminho_arquivo}', origem='FORMULAS_TRANSPORTE')
        
        todos_dados = []
        desoneracao = None
        data_base = None
        
        with open(caminho_arquivo, "rb") as arquivo:
            arquivo_pdf = PyPDF2.PdfReader(arquivo)
            num_paginas = len(arquivo_pdf.pages)
            
            log_to_file(f'PDF possui {num_paginas} páginas', origem='FORMULAS_TRANSPORTE')

            # Primeiro, vamos extrair metadados da primeira página
            texto_primeira_pagina = arquivo_pdf.pages[0].extract_text()
            desoneracao = extrair_desoneracao(texto_primeira_pagina)
            data_base = extrair_data_base(texto_primeira_pagina)
            
            log_to_file(f'Metadados extraídos - Data Base: {data_base}, Desoneração: {desoneracao}', origem='FORMULAS_TRANSPORTE')

            # Processar cada página
            for pagina_num in range(num_paginas):
                log_to_file(f'Processando página {pagina_num + 1}', origem='FORMULAS_TRANSPORTE')
                
                # Tentar extrair tabelas usando tabula
                try:
                    tabelas = tabula.read_pdf(
                        caminho_arquivo,
                        pages=pagina_num + 1,
                        encoding="utf-8",
                        guess=True,
                        lattice=True,
                        pandas_options={'dtype': str},
                        multiple_tables=True,
                        java_options=['-Dfile.encoding=UTF8']
                    )
                    
                    for tabela in tabelas:
                        if tabela.empty:
                            continue
                        
                        # Processar cada linha da tabela
                        dados_processados = processar_tabela(tabela, data_base, desoneracao)
                        if dados_processados:
                            todos_dados.extend(dados_processados)
                            
                except Exception as e:
                    log_to_file(f'Erro ao processar página {pagina_num + 1}: {str(e)}', origem='FORMULAS_TRANSPORTE')
                    continue

        if not todos_dados:
            return {"error": "Nenhum dado foi extraído do PDF"}
        
        log_to_file(f'Total de {len(todos_dados)} fórmulas extraídas', origem='FORMULAS_TRANSPORTE')
        
                # Criar DataFrame final
        df_final = pd.DataFrame(todos_dados)
        
        log_to_file(f'Total de {len(todos_dados)} fórmulas extraídas', origem='FORMULAS_TRANSPORTE')
        
        return {
            "success": True,
            "message": f"PDF processado com sucesso. {len(todos_dados)} fórmulas extraídas.",
            "data": todos_dados
        }
        
    except Exception as e:
        error_msg = f"Erro ao processar PDF: {str(e)}"
        log_to_file(error_msg, origem='FORMULAS_TRANSPORTE')
        return {"error": error_msg}

def processar_tabela(tabela, data_base, desoneracao):
    """Processa uma tabela extraída do PDF e retorna lista de fórmulas"""
    dados = []
    
    try:
        # Normalizar colunas da tabela
        colunas = normalizar_colunas(tabela)
        
        for _, row in tabela.iterrows():
            try:
                # Extrair dados da linha
                formula = extrair_formula_da_linha(row, colunas, data_base, desoneracao)
                if formula:
                    dados.append(formula)
                    
            except Exception as e:
                log_to_file(f'Erro ao processar linha da tabela: {str(e)}', origem='FORMULAS_TRANSPORTE')
                continue
                
    except Exception as e:
        log_to_file(f'Erro ao processar tabela: {str(e)}', origem='FORMULAS_TRANSPORTE')
    
    return dados

def normalizar_colunas(tabela):
    """Normaliza as colunas da tabela para facilitar o processamento"""
    colunas = []
    
    for col in tabela.columns:
        col_clean = str(col).strip().lower()
        
        if 'código' in col_clean or 'codigo' in col_clean:
            colunas.append('codigo')
        elif 'descrição' in col_clean or 'descricao' in col_clean:
            colunas.append('descricao')
        elif 'unidade' in col_clean:
            colunas.append('unidade')
        elif 'fórmula' in col_clean or 'formula' in col_clean:
            colunas.append('formula')
        else:
            colunas.append('outro')
    
    return colunas

def extrair_formula_da_linha(row, colunas, data_base, desoneracao):
    """Extrai os dados de uma linha da tabela"""
    try:
        # Mapear colunas baseado no conteúdo
        codigo = None
        descricao = None
        unidade = None
        formula = None
        
        for i, col_type in enumerate(colunas):
            valor = str(row.iloc[i]).strip()
            
            if col_type == 'codigo':
                # Código deve ser numérico
                if valor.isdigit():
                    codigo = valor
            elif col_type == 'descricao':
                # Descrição deve ter texto
                if len(valor) > 3 and not valor.isdigit():
                    descricao = valor
            elif col_type == 'unidade':
                # Unidade deve ser curta (ex: t, m³, km)
                if len(valor) <= 5:
                    unidade = valor
            elif col_type == 'formula':
                # Fórmula deve conter variáveis matemáticas
                if 'x' in valor or '+' in valor or '-' in valor or '*' in valor:
                    formula = valor
        
        # Validação dos dados extraídos
        if not codigo or not descricao or not unidade or not formula:
            return None
        
        # Limpar e validar fórmula
        formula_limpa = limpar_formula(formula)
        if not formula_limpa:
            return None
        
        return {
            'data_base': data_base,
            'desoneracao': desoneracao,
            'codigo': codigo,
            'descricao': descricao,
            'unidade': unidade,
            'formula_transporte': formula_limpa
        }
        
    except Exception as e:
        log_to_file(f'Erro ao extrair fórmula da linha: {str(e)}', origem='FORMULAS_TRANSPORTE')
        return None

def limpar_formula(formula):
    """Limpa e valida a fórmula de transporte"""
    try:
        # Remover espaços extras
        formula = re.sub(r'\s+', ' ', formula.strip())
        
        # Validar se contém elementos básicos de fórmula
        if not re.search(r'[xX]\d*', formula):  # Deve conter variável x, x1, x2
            return None
        
        # Validar se contém operadores matemáticos válidos
        if not re.search(r'[\+\-\*\/]', formula):  # Deve conter pelo menos um operador
            return None
        
        # Validar se não contém caracteres inválidos
        if re.search(r'[^0-9xX\+\-\*\/\s\.\,]', formula):
            return None
        
        return formula
        
    except Exception as e:
        log_to_file(f'Erro ao limpar fórmula: {str(e)}', origem='FORMULAS_TRANSPORTE')
        return None

def main():
    """Função principal do script"""
    if len(sys.argv) != 2:
        print(json.dumps({"error": "Uso: python script.py <caminho_pdf>"}))
        sys.exit(1)
    
    caminho_pdf = sys.argv[1]
    
    log_to_file(f'Script iniciado para processar: {caminho_pdf}', origem='FORMULAS_TRANSPORTE')
    
    # Processar PDF
    resultado = processar_pdf(caminho_pdf)
    
    # Retornar resultado em JSON
    print(json.dumps(resultado, ensure_ascii=False, indent=2))
    
    if "error" in resultado:
        sys.exit(1)
    else:
        sys.exit(0)

if __name__ == "__main__":
    main()
