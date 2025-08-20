import PyPDF2
import tabula
import pandas as pd
import json
import sys
import os
import re
from datetime import datetime

def testar_extracao_pdf(caminho_arquivo):
    """Testa a extração do PDF para debug"""
    try:
        print(f"Testando extração do PDF: {caminho_arquivo}")
        
        if not os.path.exists(caminho_arquivo):
            print("ERRO: Arquivo não encontrado")
            return
        
        with open(caminho_arquivo, "rb") as arquivo:
            arquivo_pdf = PyPDF2.PdfReader(arquivo)
            num_paginas = len(arquivo_pdf.pages)
            
            print(f"PDF possui {num_paginas} páginas")
            
            # Extrair texto da primeira página
            texto_primeira_pagina = arquivo_pdf.pages[0].extract_text()
            print(f"\n=== TEXTO PRIMEIRA PÁGINA ===")
            print(texto_primeira_pagina[:500])
            
            # Tentar extrair tabelas da primeira página
            print(f"\n=== TESTANDO EXTRACAO DE TABELAS ===")
            try:
                tabelas = tabula.read_pdf(
                    caminho_arquivo,
                    pages=1,
                    encoding="utf-8",
                    guess=True,
                    lattice=True,
                    pandas_options={'dtype': str},
                    multiple_tables=True
                )
                
                print(f"Tabelas encontradas: {len(tabelas)}")
                
                for i, tabela in enumerate(tabelas):
                    print(f"\n--- TABELA {i+1} ---")
                    print(f"Colunas: {list(tabela.columns)}")
                    print(f"Linhas: {len(tabela)}")
                    print(f"Primeiras 3 linhas:")
                    print(tabela.head(3))
                    
            except Exception as e:
                print(f"Erro ao extrair tabelas: {e}")
                
    except Exception as e:
        print(f"Erro geral: {e}")

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Uso: python teste_formulas_transporte.py <caminho_pdf>")
        sys.exit(1)
    
    caminho_pdf = sys.argv[1]
    testar_extracao_pdf(caminho_pdf)
