import re
import pdfplumber
import pandas as pd
from datetime import datetime
import os
import sys
import json

# === CONFIGURAÇÕES DE CAMINHOS ===
if len(sys.argv) < 2:
    print('Uso: python importacao.py /caminho/para/arquivo.pdf')
    sys.exit(1)

PDF_PATH = os.path.abspath(sys.argv[1])
if not os.path.isfile(PDF_PATH):
    print(f'Arquivo PDF não encontrado: {PDF_PATH}')
    sys.exit(1)

# Diretório onde o PDF está localizado será usado para os arquivos temporários
TMP_DIR = os.path.dirname(PDF_PATH)

# Diretório de logs centralizado (padrão Laravel)
LOG_FILE = os.path.abspath(os.path.join(os.path.dirname(__file__), '../../..', 'storage', 'logs', 'importacao_tabelas_oficiais.log'))
os.makedirs(os.path.dirname(LOG_FILE), exist_ok=True)

XLSX_EQUIPAMENTOS = os.path.join(TMP_DIR, 'equipamentos.xlsx')
XLSX_MAO_DE_OBRA = os.path.join(TMP_DIR, 'mao_de_obra.xlsx')
XLSX_ITENS_INCIDENCIA = os.path.join(TMP_DIR, 'itens_incidencia.xlsx')
XLSX_MATERIAIS = os.path.join(TMP_DIR, 'materiais.xlsx')
XLSX_SERVICOS = os.path.join(TMP_DIR, 'servicos.xlsx')
XLSX_TRANSPORTE = os.path.join(TMP_DIR, 'transporte.xlsx')

# Índices das páginas que queremos processar (0-base)
#PAGES_TO_READ = [0, 11, 23, 26, 32, 667, 677]

# Processa todas as páginas do PDF
with pdfplumber.open(PDF_PATH) as pdf:
    PAGES_TO_READ = list(range(len(pdf.pages)))

def log_to_file(message, origem='PYTHON'):
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    with open(LOG_FILE, 'a', encoding='utf-8') as f:
        f.write(f'[{timestamp}] [{origem}] {message}\n')

# === EXTRAÇÃO DO CABEÇALHO DO SERVIÇO ===
def extract_service_data(text):
    data_base = honerado = descricao = unidade = codigo = None

    match = re.search(r"Data Base:\s*(\d{2}/\d{2}/\d{4})\s*\((.*?)\)", text)
    if match:
        data_base, honerado = match.groups()
        log_to_file(f'Dados do serviço extraídos - Data Base: {data_base}, Honerado: {honerado}', origem='IMPORTACAO_DERPR')

    match = re.search(r"Serviço:\s*(\d+)\s+(.*?)\s+Unidade:\s*(\w+)", text)
    if match:
        codigo, descricao, unidade = match.groups()
        log_to_file(f'Dados do serviço extraídos - Código: {codigo}, Descrição: {descricao}, Unidade: {unidade}', origem='IMPORTACAO_DERPR')

    return {
        "codigo": codigo,
        "data_base": data_base,
        "honerado": honerado,
        "descricao": descricao,
        "unidade": unidade
    }

# === EXTRAÇÃO TABELA (A) EQUIPAMENTOS ===
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

# === EXTRAÇÃO TABELA (B) MÃO-DE-OBRA ===
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

# === EXTRAÇÃO TABELA (C) ITENS DE INCIDÊNCIA ===
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

# === EXTRAÇÃO TABELA (F) MATERIAIS ===
def extract_materiais(text: str, dados_servico: dict) -> list:
    materiais = []            # lista final de dicionários
    debug_log = []            # linhas de debug gravadas no log

    # Isola o bloco dos materiais
    bloco = re.search(r"\(F\)Materiais.*?\n(.*?)(\(F\)Total:)", text, re.DOTALL)
    if not bloco:
        return materiais  # Nenhum bloco → retorna vazio

    # bloco.group(1).splitlines()   ->pega o texto capturado entre "(F) Materiais" e "(F) Total:" e divide em linhas, criando uma lista
    # l.strip() (primeiro uso)      ->para cada linha l, remove espaços/brakes no início / fim
    # if l.strip()                  ->filtra: só mantém linhas que, depois do strip(), não estejam vazias
    # [ … for l in … ]              ->lista-comprehension que devolve a nova lista já "limpa"
    linhas = [l.strip() for l in bloco.group(1).splitlines() if l.strip()]
    buffer = ""  # acumula descrição + próximos tokens até validar

    # regex que aceita 1‑3 dígitos de milhar "." + vírgula decimal
    num_dec = r"\d+(?:\.\d{3})*,\d+"  # ex: 2.530,00  |  35,32

    for linha in linhas:
        buffer = linha                        # se
        
        #verificando se buffer é um registro ou somente uma continuação da descrição
        tokens = buffer.split()                      # tokeniza
        if (re.fullmatch(num_dec, tokens[-1]) and
             re.fullmatch(num_dec, tokens[-2]) and
             re.fullmatch(num_dec, tokens[-3]) ):
            #debug_log.append(f"BUFFER → {buffer}")
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
            #debug_log.append(f"ADICIONADO → {material}")
        else:
            # --- aqui de ser adicionado um código solicitado  ---  
            materiais[-1]["descricao"] += " " + buffer  # Adiciona o buffer à descrição existente  
            #debug_log.append(f"BUFFER add correto  → {materiais[-1]['descricao']}")
        
        

    # Acrescenta debug ao LOG_FILE (útil só durante testes)
    if debug_log:
        with open(LOG_FILE+"_teste", "a", encoding="utf-8") as f:
            f.write("\n=== DEBUG MATERIAIS ===\n")
            f.write("\n".join(debug_log))

    return materiais

# === EXTRAÇÃO TABELA (G) SERVIÇOS ===
def extract_servicos(text: str, dados_servico: dict) -> list:
    servicos = []             # lista final de dicionários
    debug_log = []            # linhas de debug para inspeção

    # Isola o bloco dos serviços
    bloco = re.search(r"\(G\)Serviços.*?\n(.*?)(\(G\)Total:)", text, re.DOTALL)
    if not bloco:
        return servicos       # Nenhum bloco (G) na página

    # Limpa linhas vazias
    linhas = [l.strip() for l in bloco.group(1).splitlines() if l.strip()]
    num_dec = r"\d+(?:\.\d{3})*,\d+"   # 1.456,48  |  53,47

    for linha in linhas:
        buffer = linha
        tokens = buffer.split()

        # Verifica se buffer já contém linha completa (3 decimais finais)
        if (re.fullmatch(num_dec, tokens[-1]) and
            re.fullmatch(num_dec, tokens[-2]) and
            re.fullmatch(num_dec, tokens[-3])):
            #debug_log.append(f"BUFFER → {buffer}")

            item = {
                "codigo_servico": dados_servico["codigo"],
                "descricao_servico": dados_servico["descricao"],
                "unidade_servico":  dados_servico["unidade"],
                "data_base": dados_servico["data_base"],
                "honerado":  dados_servico["honerado"],
                "descricao": " ".join(tokens[:-5]).strip(),
                "codigo":    tokens[-5],
                "unid":      tokens[-4],
                "custo_unitario":       tokens[-3],
                "consumo":              tokens[-2],
                "custo_unitario_final": tokens[-1],
            }
            servicos.append(item)
            #debug_log.append(f"ADICIONADO → {item}")
        else:
            # Linha é continuação de descrição → anexa ao último item
            if servicos:  # segurança
                servicos[-1]["descricao"] += " " + buffer
                #debug_log.append(f"BUFFER add correto → {servicos[-1]['descricao']}")

    # Grava debug (opcional) no mesmo arquivo _teste
    if debug_log:
        with open(LOG_FILE + "_teste", "a", encoding="utf-8") as f:
            f.write("\n=== DEBUG SERVIÇOS ===\n")
            f.write("\n".join(debug_log))

    return servicos


# === EXTRAÇÃO TABELA (H) ITENS DE TRANSPORTE ===
def extract_transporte(text: str, dados_servico: dict) -> list:
    transporte = []
    debug_log = []

    bloco = re.search(r"\(H\)Itens de Transporte.*?\n(.*?)(\(H\)Total:)", text, re.DOTALL)
    if not bloco:
        return transporte

    linhas = [l.strip() for l in bloco.group(1).splitlines() if l.strip()]
    num_dec = r"\d+(?:\.\d{3})*,\d+"          # 0,00 | 7,0000 | 1.456,48
    i = 0

    while i < len(linhas):
        ln = linhas[i]
        tok = ln.split()

        # Linha completa se termina com 3 decimais
        if len(tok) < 6 or not all(re.fullmatch(num_dec, t) for t in tok[-3:]):
            # é continuação (formúla2 ou descrição longa) → anexa ao último item
            if transporte:
                if 'x1' in ln.lower() and 'x2' in ln.lower():
                    transporte[-1]["formula2"] = ln
                    #debug_log.append(f"FÓRMULA2 → {ln}")
                else:
                    transporte[-1]["descricao"] += " " + ln
                    #debug_log.append(f"DESC +→ {ln}")
            i += 1
            continue

        # ------ Extrai campos da linha principal ------
        custo_unitario = tok[-1]
        consumo        = tok[-2]
        custo          = tok[-3]

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
            "codigo_servico":   dados_servico["codigo"],
            "descricao_servico":dados_servico["descricao"],
            "unidade_servico":  dados_servico["unidade"],
            "data_base":        dados_servico["data_base"],
            "honerado":         dados_servico["honerado"],
            "descricao":        descricao,
            "codigo":           codigo,
            "unid":             unid,
            "formula1":         formula1,
            "formula2":         "",        # pode ser preenchida já abaixo
            "custo":            custo,
            "consumo":          consumo,
            "custo_unitario":   custo_unitario
        }
        transporte.append(item)
        #debug_log.append(f"ADICIONADO → {item}")

        # Verifica se a próxima linha é fórmula2
        if i + 1 < len(linhas):
            prox = linhas[i + 1]
            if 'x1' in prox.lower() and 'x2' in prox.lower():
                transporte[-1]["formula2"] = prox
                #debug_log.append(f"FÓRMULA2 → {prox}")
                i += 1  # pula a linha já usada
        i += 1

    # Grava depuração
    if debug_log:
        with open(LOG_FILE + "_teste", "a", encoding="utf-8") as f:
            f.write("\n=== DEBUG TRANSPORTE ===\n")
            f.write("\n".join(debug_log))

    return transporte

# === FUNÇÃO PRINCIPAL ===
def main():
    try:
        log_to_file(f'Início da importação do arquivo PDF: {os.path.basename(PDF_PATH)}', origem='IMPORTACAO_DERPR')
        with pdfplumber.open(PDF_PATH) as pdf:
            num_pages = len(pdf.pages)
            log_to_file(f'Total de páginas no PDF: {num_pages}', origem='IMPORTACAO_DERPR')

            # coletores
            all_services = []
            all_equipamentos = []
            all_mao_de_obra = []
            all_itens_inc = []
            all_materiais = []
            all_servicos = []   # (G)
            all_transporte = []   # (H)

            for page_num in PAGES_TO_READ:
                if page_num >= num_pages:
                    log_to_file(f'AVISO: Página {page_num + 1} não existe no PDF', origem='IMPORTACAO_DERPR')
                    continue

                log_to_file(f'Processando página {page_num + 1}', origem='IMPORTACAO_DERPR')
                page = pdf.pages[page_num]
                text = page.extract_text()

                if not text:
                    log_to_file(f'AVISO: Página {page_num + 1} vazia ou não extraível', origem='IMPORTACAO_DERPR')
                    continue

                dados_servico = extract_service_data(text)
                if not dados_servico["codigo"]:
                    log_to_file(f'AVISO: Nenhum serviço encontrado na página {page_num + 1}', origem='IMPORTACAO_DERPR')
                    continue

                # Extrair e registrar dados de cada seção
                equipamentos = extract_equipamentos(text, dados_servico)
                all_equipamentos.extend(equipamentos)
                log_to_file(f'Página {page_num + 1}: {len(equipamentos)} equipamentos extraídos', origem='IMPORTACAO_DERPR')

                mao_de_obra = extract_mao_de_obra(text, dados_servico)
                all_mao_de_obra.extend(mao_de_obra)
                log_to_file(f'Página {page_num + 1}: {len(mao_de_obra)} itens de mão de obra extraídos', origem='IMPORTACAO_DERPR')

                itens_inc = extract_itens_incidencia(text, dados_servico)
                all_itens_inc.extend(itens_inc)
                log_to_file(f'Página {page_num + 1}: {len(itens_inc)} itens de incidência extraídos', origem='IMPORTACAO_DERPR')

                materiais = extract_materiais(text, dados_servico)
                all_materiais.extend(materiais)
                log_to_file(f'Página {page_num + 1}: {len(materiais)} materiais extraídos', origem='IMPORTACAO_DERPR')

                servicos = extract_servicos(text, dados_servico)
                all_servicos.extend(servicos)
                log_to_file(f'Página {page_num + 1}: {len(servicos)} serviços extraídos', origem='IMPORTACAO_DERPR')

                transportes = extract_transporte(text, dados_servico)
                all_transporte.extend(transportes)
                log_to_file(f'Página {page_num + 1}: {len(transportes)} itens de transporte extraídos', origem='IMPORTACAO_DERPR')

            # Log do resumo final
            log_to_file('\nResumo da extração:', origem='IMPORTACAO_DERPR')
            log_to_file(f'Total de equipamentos: {len(all_equipamentos)}', origem='IMPORTACAO_DERPR')
            log_to_file(f'Total de mão de obra: {len(all_mao_de_obra)}', origem='IMPORTACAO_DERPR')
            log_to_file(f'Total de itens de incidência: {len(all_itens_inc)}', origem='IMPORTACAO_DERPR')
            log_to_file(f'Total de materiais: {len(all_materiais)}', origem='IMPORTACAO_DERPR')
            log_to_file(f'Total de serviços: {len(all_servicos)}', origem='IMPORTACAO_DERPR')
            log_to_file(f'Total de transportes: {len(all_transporte)}', origem='IMPORTACAO_DERPR')

            resultado = {
                "equipamentos": all_equipamentos,
                "mao_de_obra": all_mao_de_obra,
                "itens_incidencia": all_itens_inc,
                "materiais": all_materiais,
                "servicos": all_servicos,
                "transportes": all_transporte
            }

            print(json.dumps(resultado))

    except Exception as e:
        erro = str(e)
        log_to_file(f'ERRO durante o processamento: {erro}', origem='IMPORTACAO_DERPR')
        print(json.dumps({"error": erro}))
        sys.exit(1)

# === EXECUTAR ===
if __name__ == "__main__":
    if len(sys.argv) > 1:
        log_to_file(f'Início da importação do arquivo PDF: {os.path.basename(sys.argv[1])}', origem='IMPORTACAO_DERPR')
    main()