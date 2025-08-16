<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MessageController extends Controller
{
    /**
     * Exibe uma mensagem do sistema
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:sucesso,erro,info',
            'mensagem' => 'required|string',
            'duracao' => 'nullable|integer|min:1000|max:10000'
        ]);

        $mensagem = [
            'tipo' => $request->tipo,
            'mensagem' => $request->mensagem,
            'duracao' => $request->duracao ?? 5000
        ];

        Session::flash('mensagem_sistema', $mensagem);

        return response()->json([
            'success' => true,
            'message' => 'Mensagem configurada com sucesso'
        ]);
    }

    /**
     * Limpa a mensagem atual do sistema
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        Session::forget('mensagem_sistema');

        return response()->json([
            'success' => true,
            'message' => 'Mensagem removida com sucesso'
        ]);
    }
} 