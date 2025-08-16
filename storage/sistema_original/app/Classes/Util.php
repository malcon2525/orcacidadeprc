<?php

namespace App\Classes;


 
class Util {

    


    public function __construct(){
        
    }

    //$data = string, data no formato "m/d/a"
    public function coverteDataParaYYYYMMDD($data){
        //a data, no arquivo .xlsx, está no seguinte formato: dd/mm/aaaa
        //por algum motivo, aqui ela aparece assim: mm/dd/aaaa
        //então iremos conveter para o formato aaaa-mm-dd


        // Divide a string da data em partes usando '/'
        $partes = explode('/', $data);

        // Verifica se há três partes (mês, dia, ano)
        $dataValida = true;
        
        if (count($partes) === 3) {
            $mes = $partes[0];
            $dia = $partes[1];
            $ano = $partes[2];

            // Verifica se o mês, o dia e o ano são números válidos
            if (is_numeric($mes) && is_numeric($dia) && is_numeric($ano)) {
                // Verifica se a data é válida
                if (!checkdate($mes, $dia, $ano)) {
                    $dataValida = false;
                }
            } else {
                $dataValida = false;
            }
        } else {
            $dataValida = false;
        }

        if ($dataValida == false){
            return false;
        } else {
            $dataFormatada  = "$ano-$mes-$dia";
            return $dataFormatada;

        }

    }

    //prepara string
    public function sanitizeString($string) {
        // Remover tags HTML e PHP
        $string = strip_tags($string);
        // Escapar caracteres especiais
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        // Remover espaços em branco desnecessários
        $string = trim($string);
        // Permitir caracteres ASCII estendidos, incluindo caracteres com acentos
        $string = preg_replace('/[^\x20-\x7E\xA0-\xFF]/', '', $string);
        // ^\x20-\x7E: Este é o intervalo original que representa o conjunto de caracteres ASCII básicos.
        // \xA0-\xFF: Este intervalo inclui caracteres ASCII estendidos, que abrange muitos, mas não todos, incluindo caracteres acentuados e outros símbolos comuns em várias línguas europeias.
    
        return $string;
    }

    //converte "1,234.22" em "1234.22"
    //converte "1,234.2232" em "1234.2232"
    public function formatNumberBRtoUS($numString) {
        // Remove os pontos
        $numString = str_replace('.', '', $numString);
        // Troca vírgula por ponto
        $numString = str_replace(',', '.', $numString);
        
        //converter para float
        return floatval($numString);
    }

    //converte 1,1212 para 1.12 
    //converte "1234.2232" em "1234.22"
    //converte "12345678912" em "1234567891" //maximo de 10 caracteres antes do ponto e virgula
    public function toDecimal102($valor){
        // Substitui a vírgula por ponto para trabalhar com o formato decimal
        $valorComPonto = str_replace(',', '.', $valor);

        // Encontra a posição do ponto decimal
        $posPonto = strpos($valorComPonto, '.');

        if ($posPonto !== false) {
            // Se existe um ponto, pega os 10 primeiros caracteres antes do ponto
            $antesPonto = substr($valorComPonto, 0, $posPonto);
            $antesPonto = substr($antesPonto, 0, 10);

            // Pega os caracteres após o ponto
            $depoisPonto = substr($valorComPonto, $posPonto);

            // Recompõe o valor considerando apenas os 10 primeiros caracteres antes do ponto
            $valorComPonto = $antesPonto . $depoisPonto;
        } else {
            // Se não há ponto, limita a 10 caracteres
            $valorComPonto = substr($valorComPonto, 0, 10);
        }

        // Arredonda o valor para duas casas decimais
        $valorFinal = round($valorComPonto, 2);

        return $valorFinal;
    }

    public function toUTF8($string){
        if (!mb_check_encoding($string, 'UTF-8')) {
            $return = mb_convert_encoding($string, 'UTF-8');
        } else {
            return $string;
        }
    }

    //retorna somente os números de uma determinada string
    function extractNumbers($input) {
        // Usar expressão regular para encontrar todos os dígitos na string
        preg_match_all('/\d+/', $input, $matches);
        
        // Concatenar todos os números encontrados em uma string
        $numbers = implode('', $matches[0]);
        
        return $numbers;
    }

}