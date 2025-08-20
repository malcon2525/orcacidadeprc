import PyPDF2
import sys
import os

def analisar_pdf(caminho_arquivo):
    """Analisa o conteúdo do PDF para debug"""
    try:
        with open(caminho_arquivo, "rb") as arquivo:
            arquivo_pdf = PyPDF2.PdfReader(arquivo)
            if len(arquivo_pdf.pages) == 0:
                print("PDF sem páginas")
                return
            
            print(f"PDF tem {len(arquivo_pdf.pages)} páginas")
            
            # Analisar primeira página
            texto_primeira_pagina = arquivo_pdf.pages[0].extract_text()
            texto = texto_primeira_pagina.lower()
            
            print(f"\n=== PRIMEIRA PÁGINA (primeiros 500 chars) ===")
            print(texto[:500])
            
            print(f"\n=== ANÁLISE DE PALAVRAS-CHAVE ===")
            
            # Palavras que estamos procurando
            palavras_procuradas = [
                'código', 'codigo', 'descrição', 'descricao', 'serviço', 'servico',
                'custo', 'execução', 'execucao', 'material', 'unitário', 'unitario'
            ]
            
            print("Palavras encontradas:")
            for palavra in palavras_procuradas:
                if palavra in texto:
                    print(f"✅ {palavra}")
                else:
                    print(f"❌ {palavra}")
            
            # Procurar por outras palavras que possam estar no PDF
            print(f"\n=== OUTRAS PALAVRAS ENCONTRADAS ===")
            palavras_alternativas = [
                'referencial', 'março', 'desoneração', 'composições', 'serviços',
                'tabela', 'preços', 'valores', 'grupo', 'categoria'
            ]
            
            for palavra in palavras_alternativas:
                if palavra in texto:
                    print(f"✅ {palavra}")
                else:
                    print(f"❌ {palavra}")
            
            # Mostrar todas as palavras únicas (primeiras 100)
            palavras_unicas = set(texto.split())
            print(f"\n=== PRIMEIRAS 100 PALAVRAS ÚNICAS ===")
            for i, palavra in enumerate(sorted(palavras_unicas)):
                if i >= 100:
                    break
                print(f"{i+1:3d}. {palavra}")
                
    except Exception as e:
        print(f"Erro ao analisar PDF: {str(e)}")

if __name__ == "__main__":
    if len(sys.argv) > 1:
        caminho_arquivo = sys.argv[1]
        if os.path.exists(caminho_arquivo):
            analisar_pdf(caminho_arquivo)
        else:
            print(f"Arquivo não encontrado: {caminho_arquivo}")
    else:
        print("Uso: python test_pdf_content.py <caminho_do_pdf>")
