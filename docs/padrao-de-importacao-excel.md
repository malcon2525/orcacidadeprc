# Padrão de Importação de Arquivos Excel (.xlsx)

Este documento define o padrão para implementação de importação de arquivos Excel (.xlsx) no projeto, visando garantir simplicidade, robustez e padronização. Siga estas orientações para evitar erros comuns e garantir uma experiência consistente.

---

## 1. **Bibliotecas Utilizadas**
- **phpoffice/phpspreadsheet**: Utilizada para leitura e manipulação de arquivos Excel.
- **Opcional:** maatwebsite/excel pode ser usada para importações mais complexas, mas para casos simples prefira o uso direto do PhpSpreadsheet.

> **Atenção:** Certifique-se de que a dependência está instalada no `composer.json`:
> ```json
> "phpoffice/phpspreadsheet": "^2.0"
> ```

---

## 2. **Estrutura do Arquivo Excel**
- O arquivo deve conter uma linha de cabeçalho na primeira linha.
- Os nomes das colunas devem ser **exatamente** os esperados pelo sistema, em minúsculo, sem acentos e sem espaços extras.
- Exemplo de cabeçalho:
  ```
  destino | materiais | origem | sigla_transporte | x1 | x2 | tipo
  ```
- Os dados devem começar na segunda linha.

---

## 3. **Rota de Importação**
- Crie uma rota POST dedicada para importação, por exemplo:
  ```php
  Route::post('/materiais-transporte/importar', [MaterialTransporteController::class, 'importar']);
  ```

---

## 4. **Método de Importação no Controller**
- Implemente o método de importação de forma simples, usando o PhpSpreadsheet:

```php
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Transportes\MaterialTransporte;

public function importar(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls',
    ]);

    $file = $request->file('file');
    $spreadsheet = IOFactory::load($file->getPathname());
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    // Descobrir os índices das colunas pelo cabeçalho
    $header = array_map('strtolower', $rows[1]);
    $map = array_flip($header);

    for ($i = 2; $i <= count($rows); $i++) {
        $row = $rows[$i];
        MaterialTransporte::create([
            'destino' => $row[$map['destino']] ?? '',
            'materiais' => $row[$map['materiais']] ?? '',
            'origem' => $row[$map['origem']] ?? null,
            'sigla_transporte' => $row[$map['sigla_transporte']] ?? '',
            'x1' => isset($row[$map['x1']]) ? str_replace(',', '.', $row[$map['x1']]) : 0,
            'x2' => isset($row[$map['x2']]) ? str_replace(',', '.', $row[$map['x2']]) : 0,
            'tipo' => $row[$map['tipo']] ?? '',
        ]);
    }

    return response()->json(['success' => true], 200);
}
```

---

## 5. **Frontend (Vue.js)**
- Use um `<input type="file">` para upload do arquivo.
- Envie o arquivo via `FormData` para a rota de importação.
- Exemplo de método:

```js
import axios from 'axios';

methods: {
    importarArquivo(event) {
        const file = event.target.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);
        axios.post('/materiais-transporte/importar', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(() => {
            alert('Importação realizada com sucesso!');
            this.$emit('atualizar');
        })
        .catch(() => {
            alert('Erro ao importar arquivo.');
        });
    },
}
```

---

## 6. **Boas Práticas e Dicas para Evitar Erros**
- **Valide o arquivo** antes de processar (tipo, extensão, presença do cabeçalho).
- **Garanta que os nomes das colunas** no Excel estejam corretos e sem espaços/acentos.
- **Trate valores numéricos**: converta vírgula para ponto se necessário.
- **Retorne sempre um JSON** com status 200 em caso de sucesso para evitar erros no frontend.
- **No frontend, trate o sucesso e o erro** com alertas simples para feedback ao usuário.
- **Evite complexidade desnecessária**: só use classes de importação se realmente precisar de lógica avançada.
- **Verifique permissões de escrita** na pasta `storage` para evitar erros de upload temporário.
- **Cheque o log do Laravel** (`storage/logs/laravel.log`) em caso de erro 500 para identificar rapidamente o problema.

---

## 7. **Exemplo de Cabeçalho Correto para Excel**
| destino         | materiais                  | origem         | sigla_transporte | x1    | x2    | tipo      |
|-----------------|---------------------------|----------------|------------------|-------|-------|-----------|
| Trecho da Obra  | Areia                     | Areal          | LCB              | 10,00 | 0,00  | local     |
| ...             | ...                       | ...            | ...              | ...   | ...   | ...       |

---

## 8. **Checklist para Importação**
- [ ] O arquivo está em formato `.xlsx` ou `.xls`?
- [ ] O cabeçalho está correto, sem acentos ou espaços?
- [ ] As colunas obrigatórias estão presentes?
- [ ] O backend retorna status 200 e JSON em caso de sucesso?
- [ ] O frontend exibe feedback claro ao usuário?

---

**Seguindo este padrão, a importação de arquivos Excel será simples, robusta e padronizada em todo o projeto.** 