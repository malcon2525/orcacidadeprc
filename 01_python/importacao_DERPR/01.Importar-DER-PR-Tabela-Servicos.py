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
    log_file = os.path.abspath(os.path.join(os.path.dirname(__file__), '../../..', 'storage', 'logs', 'importacao_tabelas_oficiais.log'))
    os.makedirs(os.path.dirname(log_file), exist_ok=True)
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    with open(log_file, 'a', encoding='utf-8') as f:
        f.write(f'[{timestamp}] [{origem}] {message}\n')

def extrair_desoneracao(texto):
    if "Sem desoneração" in texto:
        return "Sem desoneração"
    elif "Com desoneração" in texto:
        return "Com desoneração"
    return "Sem desoneração"  # valor padrão



def processar_pdf(caminho_arquivo):
    try:
        if not os.path.exists(caminho_arquivo):
            return {"error": f'Arquivo não encontrado: {caminho_arquivo}'}
        
        todos_dados = []
        desoneracao = None
        
        with open(caminho_arquivo, "rb") as arquivo:
            arquivo_pdf = PyPDF2.PdfReader(arquivo)
            num_paginas = len(arquivo_pdf.pages)

            # Primeiro, vamos verificar a desoneração na primeira página
            texto_primeira_pagina = arquivo_pdf.pages[0].extract_text()
            desoneracao = extrair_desoneracao(texto_primeira_pagina)

            for pagina_num in range(num_paginas):
                texto_pagina = arquivo_pdf.pages[pagina_num].extract_text()
                data_base = extrair_data_base(texto_pagina)
                grupo_servico = extrair_grupo_servico(texto_pagina)
                
                # Debug: mostrar texto das primeiras páginas
                if pagina_num < 3:
                    print(f"Texto da página {pagina_num + 1}: {texto_pagina[:300]}...", file=sys.stderr)
                
                if not data_base or not grupo_servico:
                    continue
                
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
                        
                    tabela_normalizada = normalizar_colunas(tabela)
                    tabela_normalizada['data_base'] = data_base
                    tabela_normalizada['grupo_servico'] = grupo_servico
                    tabela_normalizada['honerado'] = desoneracao
                    todos_dados.append(tabela_normalizada)

        if not todos_dados:
            return {"error": "Nenhum dado foi extraído do PDF"}
        
        df_final = pd.concat(todos_dados, ignore_index=True)
        
        # Converter para array estruturado
        dados_estruturados = []
        for _, row in df_final.iterrows():
            try:
                servico = {
                    'grupo': str(row['grupo_servico']).strip(),
                    'data_base': str(row['data_base']).strip(),
                    'honerado': str(row['honerado']).strip(),
                    'codigo': str(row['codigo']).strip(),
                    'descricao': str(row['descricao']).strip(),
                    'unidade': str(row['unidade']).strip(),
                    'custo_execucao': str(row['custo_execucao']).strip(),
                    'custo_material': str(row['custo_material']).strip(),
                    'custo_sub_servico': str(row['custo_sub_servico']).strip(),
                    'custo_unitario': str(row['custo_unitario']).strip(),
                    'transporte': str(row['transporte']).strip()
                }
                dados_estruturados.append(servico)
            except Exception as e:
                print(f"Erro ao processar linha: {e}", file=sys.stderr)
                continue
        
        if not dados_estruturados:
            return {"error": "Nenhum dado válido foi extraído do PDF"}
            
        return dados_estruturados

    except Exception as e:
        return {"error": str(e)}

def extrair_data_base(texto):
    match = re.search(r"Data Base: (\d{2}/\d{2}/\d{4})", texto)
    return match.group(1) if match else None

def extrair_grupo_servico(texto):
    match = re.search(r"Grupo de serviço: (.+)", texto)
    return match.group(1) if match else None

def converter_valor_br(valor):
    if pd.isna(valor) or valor == '':
        return 0.0
    try:
        # Remove espaços e substitui vírgula por ponto
        valor = str(valor).strip().replace('.', '').replace(',', '.')
        return float(valor)
    except:
        return 0.0

def normalizar_colunas(tabela):
    colunas_mapeadas = {
        'Código': 'codigo',
        'Descrição do Serviço': 'descricao',
        'Unidade': 'unidade',
        'Custo Execução': 'custo_execucao',
        'Custo Material': 'custo_material',
        'Custo Sub-Serviço': 'custo_sub_servico',
        'Custo Unitário': 'custo_unitario',
        'Transporte': 'transporte'
    }
    
    df_normalizado = pd.DataFrame()
    
    for col_antiga, col_nova in colunas_mapeadas.items():
        if col_antiga in tabela.columns:
            if col_nova in ['custo_execucao', 'custo_material', 'custo_sub_servico', 'custo_unitario']:
                # Converte valores monetários normalmente
                df_normalizado[col_nova] = tabela[col_antiga].apply(converter_valor_br).apply(lambda x: f"{x:,.2f}".replace(",", "X").replace(".", ",").replace("X", "."))
            elif col_nova == 'transporte':
                # Para transporte, sempre string: mantém 'A acrescer', vazio, ou o texto original
                def trata_transporte(val):
                    val_str = str(val).strip()
                    if val_str == '' or val_str.lower() == 'nan':
                        return ''
                    if val_str.lower() == 'a acrescer':
                        return 'A acrescer'
                    return val_str
                df_normalizado[col_nova] = tabela[col_antiga].apply(trata_transporte)
            else:
                df_normalizado[col_nova] = tabela[col_antiga].fillna('')
        else:
            if col_nova in ['custo_execucao', 'custo_material', 'custo_sub_servico', 'custo_unitario']:
                df_normalizado[col_nova] = '0,00'
            elif col_nova == 'transporte':
                df_normalizado[col_nova] = ''
            else:
                df_normalizado[col_nova] = ''
    
    return df_normalizado

if __name__ == "__main__":
    try:
        if len(sys.argv) > 1:
            log_to_file(f'Início da importação do arquivo PDF: {os.path.basename(sys.argv[1])}', origem='SERVICOS_GERAIS')
            caminho_arquivo = sys.argv[1]
            dados = processar_pdf(caminho_arquivo)
            
            # Verificar se há erro
            if isinstance(dados, dict) and "error" in dados:
                print(json.dumps({"error": dados["error"]}, ensure_ascii=False))
            else:
                # Garantir que todos os valores são strings
                dados_limpos = []
                for item in dados:
                    try:
                        item_limpo = {}
                        for k, v in item.items():
                            if pd.isna(v):
                                item_limpo[k] = ''
                            else:
                                item_limpo[k] = str(v).strip()
                        dados_limpos.append(item_limpo)
                    except Exception as e:
                        continue

                # Enviar dados como JSON
                print(json.dumps(dados_limpos, ensure_ascii=False))
            
            sys.stdout.flush()
        else:
            print(json.dumps({"error": "Caminho do arquivo não fornecido"}))
            sys.stdout.flush()
    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.stdout.flush()