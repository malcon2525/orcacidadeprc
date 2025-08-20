import re
import pdfplumber
import json
import sys
from datetime import datetime
import logging
import os

# Configurar logging para suprimir avisos do pdfplumber
logging.getLogger('pdfminer').setLevel(logging.ERROR)

# Função para log centralizado
def log_to_file(message, origem='PYTHON'):
    log_file = os.path.abspath(os.path.join(os.path.dirname(__file__), '../../..', 'storage', 'logs', 'importacao_tabelas_oficiais.log'))
    os.makedirs(os.path.dirname(log_file), exist_ok=True)
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    with open(log_file, 'a', encoding='utf-8') as f:
        f.write(f'[{timestamp}] [{origem}] {message}\n')

def extract_service_data(text):
    data_base = honerado = descricao = unidade = codigo = None
    
    log_to_file(f'DEBUG: Iniciando extração de dados do serviço', origem='INSUMOS')
    log_to_file(f'DEBUG: Texto da página (primeiros 500 chars): {text[:500]}', origem='INSUMOS')

    match = re.search(r"Data Base:\s*(\d{2}/\d{2}/\d{4})\s*\((.*?)\)", text)
    if match:
        data_base, honerado = match.groups()
        log_to_file(f'Dados do serviço extraídos - Data Base: {data_base}, Honerado: {honerado}', origem='INSUMOS')
    else:
        log_to_file(f'ERRO: Regex Data Base não encontrou match. Texto procurado: {text[:200]}', origem='INSUMOS')

    match = re.search(r"Serviço:\s*(\d+)\s+(.*?)\s+Unidade:\s*(\w+)", text)
    if match:
        codigo, descricao, unidade = match.groups()
        log_to_file(f'Dados do serviço extraídos - Código: {codigo}, Descrição: {descricao}, Unidade: {unidade}', origem='INSUMOS')
    else:
        log_to_file(f'ERRO: Regex Serviço não encontrou match. Texto procurado: {text[:300]}', origem='INSUMOS')

    return {
        "codigo": codigo,
        "data_base": data_base,
        "honerado": honerado,
        "descricao": descricao,
        "unidade": unidade
    }

def extract_equipamentos(text, dados_servico):
    equipamentos = []
    match = re.search(r"\(A\)Equipamento.*?\n(.*?)(\(A\)Total:)", text, re.DOTALL)
    if not match:
        return equipamentos
    tabela_texto = match.group(1).strip()

    for linha in tabela_texto.splitlines():
        if not linha.strip():
            continue
        partes = linha.strip().split()
        if len(partes) < 9:
            continue
        custo_horario = partes[-1]
        vl_hr_imp = partes[-2]
        vl_hr_prod = partes[-3]
        ut_improdutiva = partes[-4]
        ut_produtiva = partes[-5]
        quantidade = partes[-6]
        codigo_equip = partes[-7]
        descricao = " ".join(partes[:-7])
        equipamento = {
            "codigo_servico": dados_servico["codigo"],
            "descricao_servico": dados_servico["descricao"],
            "unidade_servico": dados_servico["unidade"],
            "data_base": dados_servico["data_base"],
            "honerado": dados_servico["honerado"],
            "descricao": descricao,
            "codigo_equipamento": codigo_equip,
            "quantidade": quantidade,
            "ut_produtiva": ut_produtiva,
            "ut_improdutiva": ut_improdutiva,
            "vl_hr_prod": vl_hr_prod,
            "vl_hr_imp": vl_hr_imp,
            "custo_horario": custo_horario
        }
        equipamentos.append(equipamento)
    return equipamentos

def extract_mao_de_obra(text, dados_servico):
    mao_de_obra = []
    match = re.search(r"\(B\)Mão-de-Obra.*?\n(.*?)(\(B\)Total:)", text, re.DOTALL)
    if not match:
        return mao_de_obra
    tabela_texto = match.group(1).strip()

    for linha in tabela_texto.splitlines():
        if not linha.strip():
            continue
        partes = linha.strip().split()
        if len(partes) < 7:
            continue
        custo_horario = partes[-1]
        consumo = partes[-2]
        sal_hora = partes[-3]
        encargos_percentagem = partes[-4]
        eq_salarial = partes[-5]
        codigo = partes[-6]
        descricao = " ".join(partes[:-6])
        item = {
            "codigo_servico": dados_servico["codigo"],
            "descricao_servico": dados_servico["descricao"],
            "unidade_servico": dados_servico["unidade"],
            "data_base": dados_servico["data_base"],
            "honerado": dados_servico["honerado"],
            "descricao": descricao,
            "codigo": codigo,
            "eq_salarial": eq_salarial,
            "encargos_percentagem": encargos_percentagem,
            "sal_hora": sal_hora,
            "consumo": consumo,
            "custo_horario": custo_horario
        }
        mao_de_obra.append(item)
    return mao_de_obra

def extract_itens_incidencia(text, dados_servico):
    incidencia = []
    match = re.search(r"\(C\)Itens de Incidência.*?\n(.*?)(\(C\)Total:)", text, re.DOTALL)
    if not match:
        return incidencia
    tabela_texto = match.group(1).strip()

    for linha in tabela_texto.splitlines():
        if not linha.strip():
            continue
        partes = linha.strip().split()
        if len(partes) < 4:
            continue
        custo = partes[-1]

        if partes[-2] == "X":
            tem_mo = True
            percentagem = partes[-3]
            codigo = partes[-4]
            descricao = " ".join(partes[:-4])
        else:
            tem_mo = False
            percentagem = partes[-2]
            codigo = partes[-3]
            descricao = " ".join(partes[:-3])

        item = {
            "codigo_servico": dados_servico["codigo"],
            "descricao_servico": dados_servico["descricao"],
            "unidade_servico": dados_servico["unidade"],
            "data_base": dados_servico["data_base"],
            "honerado": dados_servico["honerado"],
            "descricao": descricao,
            "codigo": codigo,
            "percentagem": percentagem,
            "tem_mo": tem_mo,
            "custo": custo
        }
        incidencia.append(item)
    return incidencia

def extract_materiais(text, dados_servico):
    materiais = []
    num_dec = r"\d+(?:\.\d{3})*,\d+"

    bloco = re.search(r"\(F\)Materiais.*?\n(.*?)(\(F\)Total:)", text, re.DOTALL)
    if not bloco:
        return materiais

    linhas = [l.strip() for l in bloco.group(1).splitlines() if l.strip()]
    buffer = ""

    for linha in linhas:
        buffer = linha
        tokens = buffer.split()
        
        if (re.fullmatch(num_dec, tokens[-1]) and
            re.fullmatch(num_dec, tokens[-2]) and
            re.fullmatch(num_dec, tokens[-3])):
            material = {
                "codigo_servico": dados_servico["codigo"],
                "descricao_servico": dados_servico["descricao"],
                "unidade_servico": dados_servico["unidade"],
                "data_base": dados_servico["data_base"],
                "honerado": dados_servico["honerado"],
                "descricao": " ".join(tokens[:-5]).strip(),
                "codigo": tokens[-5],
                "unid": tokens[-4],
                "custo_unitario": tokens[-3],
                "consumo": tokens[-2],
                "custo_unitario_final": tokens[-1],
            }
            materiais.append(material)
        else:
            if materiais:
                materiais[-1]["descricao"] += " " + buffer

    return materiais

def extract_servicos(text, dados_servico):
    servicos = []
    num_dec = r"\d+(?:\.\d{3})*,\d+"

    bloco = re.search(r"\(G\)Serviços.*?\n(.*?)(\(G\)Total:)", text, re.DOTALL)
    if not bloco:
        return servicos

    linhas = [l.strip() for l in bloco.group(1).splitlines() if l.strip()]

    for linha in linhas:
        buffer = linha
        tokens = buffer.split()

        if (re.fullmatch(num_dec, tokens[-1]) and
            re.fullmatch(num_dec, tokens[-2]) and
            re.fullmatch(num_dec, tokens[-3])):
            item = {
                "codigo_servico": dados_servico["codigo"],
                "descricao_servico": dados_servico["descricao"],
                "unidade_servico": dados_servico["unidade"],
                "data_base": dados_servico["data_base"],
                "honerado": dados_servico["honerado"],
                "descricao": " ".join(tokens[:-5]).strip(),
                "codigo": tokens[-5],
                "unid": tokens[-4],
                "custo_unitario": tokens[-3],
                "consumo": tokens[-2],
                "custo_unitario_final": tokens[-1],
            }
            servicos.append(item)
        else:
            if servicos:
                servicos[-1]["descricao"] += " " + buffer

    return servicos

def extract_transporte(text, dados_servico):
    transportes = []
    num_dec = r"\d+(?:\.\d{3})*,\d+"

    bloco = re.search(r"\(H\)Itens de Transporte.*?\n(.*?)(\(H\)Total:)", text, re.DOTALL)
    if not bloco:
        return transportes

    linhas = [l.strip() for l in bloco.group(1).splitlines() if l.strip()]
    i = 0

    while i < len(linhas):
        ln = linhas[i]
        tok = ln.split()

        # Linha completa se termina com 3 decimais
        if len(tok) < 6 or not all(re.fullmatch(num_dec, t) for t in tok[-3:]):
            # é continuação (formúla2 ou descrição longa) → anexa ao último item
            if transportes:
                if 'x1' in ln.lower() and 'x2' in ln.lower():
                    transportes[-1]["formula2"] = ln
                else:
                    transportes[-1]["descricao"] += " " + ln
            i += 1
            continue

        # ------ Extrai campos da linha principal ------
        custo_unitario = tok[-1]
        consumo = tok[-2]
        custo = tok[-3]

        # Caminha de trás p/ frente para achar unidade (somente letras)
        j = -4
        while abs(j) <= len(tok) and not re.fullmatch(r"[A-Za-z]+", tok[j]):
            j -= 1
        unid = tok[j] if abs(j) <= len(tok) else ""
        codigo = tok[j-1] if abs(j-1) <= len(tok) and re.fullmatch(r"\d+", tok[j-1]) else ""

        # Tudo entre codigo/unid e os 3 números finais = formula1
        formula_tokens = tok[j+1:-3]
        formula1 = " ".join(formula_tokens).strip()

        # Tudo antes do código = descrição
        descricao = " ".join(tok[:j-1]).strip()

        item = {
            "codigo_servico": dados_servico["codigo"],
            "descricao_servico": dados_servico["descricao"],
            "unidade_servico": dados_servico["unidade"],
            "data_base": dados_servico["data_base"],
            "honerado": dados_servico["honerado"],
            "descricao": descricao,
            "codigo": codigo,
            "unid": unid,
            "formula1": formula1,
            "formula2": "",
            "custo": custo,
            "consumo": consumo,
            "custo_unitario": custo_unitario
        }
        transportes.append(item)

        # Verifica se a próxima linha é fórmula2
        if i + 1 < len(linhas):
            prox = linhas[i + 1]
            if 'x1' in prox.lower() and 'x2' in prox.lower():
                transportes[-1]["formula2"] = prox
                i += 1  # pula a linha já usada
        i += 1

    return transportes

def main():
    if len(sys.argv) < 2:
        log_to_file('ERRO: Caminho do arquivo PDF não fornecido', origem='INSUMOS')
        print(json.dumps({"error": "Caminho do arquivo PDF não fornecido"}))
        sys.exit(1)

    pdf_path = sys.argv[1]
    resultado = {
        "equipamentos": [],
        "mao_de_obra": [],
        "itens_incidencia": [],
        "materiais": [],
        "servicos": [],
        "transportes": []
    }

    try:
        log_to_file(f'Iniciando processamento do arquivo: {os.path.basename(pdf_path)}', origem='INSUMOS')
        with pdfplumber.open(pdf_path) as pdf:
            total_paginas = len(pdf.pages)
            log_to_file(f'Total de páginas no PDF: {total_paginas}', origem='INSUMOS')

            for page_num, page in enumerate(pdf.pages, 1):
                log_to_file(f'Processando página {page_num} de {total_paginas}', origem='INSUMOS')
                text = page.extract_text()
                if not text:
                    log_to_file(f'Página {page_num} vazia ou não extraível', origem='INSUMOS')
                    continue
                
                log_to_file(f'DEBUG: Página {page_num} - Tamanho do texto: {len(text)} caracteres', origem='INSUMOS')
                log_to_file(f'DEBUG: Página {page_num} - Primeiros 200 chars: {text[:200]}', origem='INSUMOS')

                dados_servico = extract_service_data(text)
                if not dados_servico["codigo"]:
                    log_to_file(f'Nenhum serviço encontrado na página {page_num}', origem='INSUMOS')
                    continue

                # Extrair e registrar dados de cada seção
                equipamentos = extract_equipamentos(text, dados_servico)
                resultado["equipamentos"].extend(equipamentos)
                log_to_file(f'Página {page_num}: {len(equipamentos)} equipamentos extraídos', origem='INSUMOS')
                if equipamentos:
                    log_to_file(f'DEBUG: Equipamentos encontrados: {equipamentos[:2]}', origem='INSUMOS')

                mao_de_obra = extract_mao_de_obra(text, dados_servico)
                resultado["mao_de_obra"].extend(mao_de_obra)
                log_to_file(f'Página {page_num}: {len(mao_de_obra)} itens de mão de obra extraídos', origem='INSUMOS')

                itens_incidencia = extract_itens_incidencia(text, dados_servico)
                resultado["itens_incidencia"].extend(itens_incidencia)
                log_to_file(f'Página {page_num}: {len(itens_incidencia)} itens de incidência extraídos', origem='INSUMOS')

                materiais = extract_materiais(text, dados_servico)
                resultado["materiais"].extend(materiais)
                log_to_file(f'Página {page_num}: {len(materiais)} materiais extraídos', origem='INSUMOS')

                servicos = extract_servicos(text, dados_servico)
                resultado["servicos"].extend(servicos)
                log_to_file(f'Página {page_num}: {len(servicos)} serviços extraídos', origem='INSUMOS')

                transportes = extract_transporte(text, dados_servico)
                resultado["transportes"].extend(transportes)
                log_to_file(f'Página {page_num}: {len(transportes)} itens de transporte extraídos', origem='INSUMOS')

        # Log do resumo final
        log_to_file('\nResumo da extração:', origem='INSUMOS')
        log_to_file(f'Total de equipamentos: {len(resultado["equipamentos"])}', origem='INSUMOS')
        log_to_file(f'Total de mão de obra: {len(resultado["mao_de_obra"])}', origem='INSUMOS')
        log_to_file(f'Total de itens de incidência: {len(resultado["itens_incidencia"])}', origem='INSUMOS')
        log_to_file(f'Total de materiais: {len(resultado["materiais"])}', origem='INSUMOS')
        log_to_file(f'Total de serviços: {len(resultado["servicos"])}', origem='INSUMOS')
        log_to_file(f'Total de transportes: {len(resultado["transportes"])}', origem='INSUMOS')

        print(json.dumps(resultado))

    except Exception as e:
        erro = str(e)
        log_to_file(f'ERRO durante o processamento: {erro}', origem='INSUMOS')
        print(json.dumps({"error": erro}))
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) > 1:
        log_to_file(f'Início da importação do arquivo PDF: {os.path.basename(sys.argv[1])}', origem='INSUMOS')
    main() 