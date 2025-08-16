<?php

namespace App\Http\Controllers\Api\Diversos;

use App\Http\Controllers\Controller;
use App\Models\Api\Preco\Municipio;
use Illuminate\Http\Request;

class UtilController extends Controller
{
    
    public function importarMunicipios(Request $request){
          
        $data = $request->data;
        foreach ($data as $municipioData) {
            Municipio::updateOrCreate(
                ['codigo_ibge' => $municipioData['codigo_ibge']],
                [
                    'nome' => $municipioData['nome'],
                    'prefeito' => $municipioData['prefeito'],
                    'email' => $municipioData['email'],
                    'endereco_prefeitura' => $municipioData['endereco_prefeitura'],
                    'populacao' => $municipioData['populacao'],
                    'cep' => $municipioData['cep'],
                    'telefone' => $municipioData['telefone'],
                    'cnpj' => $municipioData['cnpj'],
                ]
            );
        }

        return response()->json(['message' => 'Dados importados com sucesso'], 200);
    }

    public function listarMunicipios(Request $request){
          
        $data = Municipio::all()->map(function ($municipio) {
            return [
                'id' => $municipio->id,
                'nome' => $municipio->nome,
                'prefeito' => $municipio->prefeito,
                'email' => $municipio->email,
                'endereco_prefeitura' => $municipio->endereco_prefeitura,
                'codigo_ibge' => $municipio->codigo_ibge,
                'populacao' => $municipio->populacao,
                'cep' => $this->formatCep($municipio->cep),
                'telefone' => $municipio->telefone,
                'cnpj' => $this->formatCnpj($municipio->cnpj),
            ];
        });
    
        return response()->json($data, 200);
    }

    

    private function formatCnpj($cnpj){
        //return $cnpj;
        /// Formatar CNPJ no formato 99.999.999/9999-99
        $cnpj = substr_replace($cnpj, '.', 2, 0);
        $cnpj = substr_replace($cnpj, '.', 6, 0);
        $cnpj = substr_replace($cnpj, '/', 10, 0);
        $cnpj =  substr_replace($cnpj, '-', 15, 0);
        return $cnpj;
        }

    private function formatCep($cep){
        // Formatar CEP no formato 99999-999
        return preg_replace("/(\d{5})(\d{3})/", "\$1-\$2", $cep);
    }
}
