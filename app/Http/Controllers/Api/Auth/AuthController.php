<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Processa o login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('=== INÍCIO TENTATIVA DE LOGIN ===', [
            'email' => $request->email,
            'timestamp' => now()
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('Usuário não encontrado', ['email' => $request->email]);
            throw ValidationException::withMessages([
                'email' => ['Não foi possível encontrar uma conta com este endereço de e-mail. Verifique se o e-mail está correto e tente novamente.'],
            ]);
        }

        Log::info('Usuário encontrado', [
            'user_id' => $user->id,
            'name' => $user->name,
            'login_type' => $user->login_type ?? 'local',
            'is_active' => $user->is_active ?? true
        ]);

        // Verificar se o usuário está ativo (se o campo existir)
        if (isset($user->is_active) && !$user->is_active) {
            Log::warning('Usuário inativo tentou login', ['user_id' => $user->id]);
            throw ValidationException::withMessages([
                'email' => ['Esta conta está temporariamente desativada. Entre em contato com o administrador do sistema para obter assistência.'],
            ]);
        }

        // Verificar senha baseado no tipo de login
        $passwordValid = false;
        
        if ($user->login_type === 'ad') {
            Log::info('=== AUTENTICAÇÃO AD INICIADA ===', [
                'user_id' => $user->id,
                'email' => $request->email,
                'login_type' => $user->login_type
            ]);
            
            // Autenticar no Active Directory
            $passwordValid = $this->authenticateWithAD($request->email, $request->password);
            
            Log::info('=== RESULTADO AUTENTICAÇÃO AD ===', [
                'user_id' => $user->id,
                'login_type' => $user->login_type,
                'password_valid' => $passwordValid,
                'email' => $request->email
            ]);
        } else {
            Log::info('=== AUTENTICAÇÃO LOCAL INICIADA ===', [
                'user_id' => $user->id,
                'email' => $request->email,
                'login_type' => $user->login_type ?? 'local'
            ]);
            
            // Autenticar localmente
            $passwordValid = Hash::check($request->password, $user->password);
            
            Log::info('=== RESULTADO AUTENTICAÇÃO LOCAL ===', [
                'user_id' => $user->id,
                'login_type' => $user->login_type ?? 'local',
                'password_valid' => $passwordValid,
                'password_provided_length' => strlen($request->password)
            ]);
        }

        if (!$passwordValid) {
            Log::warning('Senha inválida', [
                'user_id' => $user->id,
                'email' => $request->email,
                'login_type' => $user->login_type ?? 'local'
            ]);
            throw ValidationException::withMessages([
                'email' => ['A senha informada está incorreta. Verifique suas credenciais e tente novamente.'],
            ]);
        }

        Log::info('Login bem-sucedido', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        // Fazer login na sessão
        Auth::login($user, $request->boolean('remember'));

        // Se for uma requisição API, retorna JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login realizado com sucesso',
                'user' => $user,
                'redirect' => route('home')
            ]);
        }

        // Se for uma requisição web (formulário), redireciona para home
        return redirect()->intended(route('home'));
    }

    /**
     * Processa o logout
     */
    public function logout(Request $request)
    {
        Log::info('Logout realizado', [
            'user_id' => Auth::id(),
            'email' => Auth::user()->email ?? 'N/A'
        ]);

        // Fazer logout da sessão
        Auth::logout();

        // Invalidar sessão
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Se for uma requisição API, retorna JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logout realizado com sucesso'
            ]);
        }

        // Se for uma requisição web (formulário), redireciona para login
        return redirect()->route('login');
    }

    /**
     * Verifica se o usuário está autenticado (para API)
     */
    public function me(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Usuário não autenticado'], 401);
        }

        $user = User::with('roles.permissions')->find(Auth::id());
        
        return response()->json($user);
    }

    /**
     * Autenticar usuário no Active Directory
     */
    private function authenticateWithAD($email, $password)
    {
        Log::info('=== MÉTODO authenticateWithAD INICIADO ===', [
            'email' => $email,
            'ad_enabled' => config('adldap.connections.default.enabled', false)
        ]);
        
        try {
            // Verificar se AD está habilitado
            if (!config('adldap.connections.default.enabled', false)) {
                Log::warning('Tentativa de autenticação AD com AD desabilitado', ['email' => $email]);
                return false;
            }

            Log::info('AD está habilitado, criando serviço...', ['email' => $email]);
            $adService = new \App\Services\ActiveDirectoryService();
            
            // Conectar ao AD
            Log::info('Tentando conectar ao AD...', ['email' => $email]);
            if (!$adService->connect()) {
                Log::error('Falha ao conectar ao AD para autenticação', ['email' => $email]);
                return false;
            }
            Log::info('Conexão com AD estabelecida com sucesso', ['email' => $email]);

            // Buscar usuário no AD
            Log::info('Buscando usuário no AD...', ['email' => $email]);
            $adUser = $adService->findUserByEmail($email);
            
            if (!$adUser) {
                Log::warning('Usuário não encontrado no AD', ['email' => $email]);
                return false;
            }
            Log::info('Usuário encontrado no AD', [
                'email' => $email,
                'ad_username' => $adUser['username'],
                'ad_name' => $adUser['name']
            ]);

            // Tentar autenticar com as credenciais fornecidas
            Log::info('Tentando autenticar usuário no AD...', [
                'email' => $email,
                'username' => $adUser['username']
            ]);
            $authenticated = $adService->authenticateUser($adUser['username'], $password);
            
            Log::info('=== RESULTADO FINAL AUTENTICAÇÃO AD ===', [
                'email' => $email,
                'username' => $adUser['username'],
                'authenticated' => $authenticated
            ]);

            return $authenticated;

        } catch (\Exception $e) {
            Log::error('Erro na autenticação AD', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
