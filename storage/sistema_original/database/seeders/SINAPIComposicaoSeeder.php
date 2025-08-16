<?php

namespace Database\Seeders;

use App\Models\SINAPIComposicao;
use Illuminate\Database\Seeder;

class SINAPIComposicaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dados de exemplo para a tabela SINAPI
        $dados = [
            [
                'codigo' => '12345',
                'descricao' => 'Serviço de terraplenagem com escavadeira',
                'unidade' => 'm³',
                'custo_unitario' => 15.50,
                'data_base' => '2024-03-01',
                'desoneracao' => 'com'
            ],
            [
                'codigo' => '12345',
                'descricao' => 'Serviço de terraplenagem com escavadeira',
                'unidade' => 'm³',
                'custo_unitario' => 14.50,
                'data_base' => '2024-03-01',
                'desoneracao' => 'sem'
            ],
            [
                'codigo' => '67890',
                'descricao' => 'Concreto armado fck 20 MPa',
                'unidade' => 'm³',
                'custo_unitario' => 450.00,
                'data_base' => '2024-03-01',
                'desoneracao' => 'com'
            ],
            [
                'codigo' => '67890',
                'descricao' => 'Concreto armado fck 20 MPa',
                'unidade' => 'm³',
                'custo_unitario' => 420.00,
                'data_base' => '2024-03-01',
                'desoneracao' => 'sem'
            ],
            [
                'codigo' => '54321',
                'descricao' => 'Alvenaria de tijolo cerâmico',
                'unidade' => 'm²',
                'custo_unitario' => 85.00,
                'data_base' => '2024-03-01',
                'desoneracao' => 'com'
            ],
            [
                'codigo' => '54321',
                'descricao' => 'Alvenaria de tijolo cerâmico',
                'unidade' => 'm²',
                'custo_unitario' => 80.00,
                'data_base' => '2024-03-01',
                'desoneracao' => 'sem'
            ]
        ];

        // Insere os dados na tabela
        foreach ($dados as $dado) {
            SINAPIComposicao::create($dado);
        }
    }
} 