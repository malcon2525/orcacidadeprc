<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ===================================================================
// ROTAS PÚBLICAS (SEM AUTENTICAÇÃO)
// ===================================================================

// Rota pública - página inicial
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ===== ROTAS WEB - INTERFACE =====
Route::get('/login', [App\Http\Controllers\Web\Auth\AuthController::class, 'showLoginForm'])->name('login');

// ===== ROTAS API - DADOS =====
Route::prefix('api/auth')->name('api.auth.')->group(function () {
    Route::post('/login', [App\Http\Controllers\Api\Auth\AuthController::class, 'login'])->name('login');
    Route::post('/logout', [App\Http\Controllers\Api\Auth\AuthController::class, 'logout'])->middleware('auth')->name('logout');
    Route::get('/me', [App\Http\Controllers\Api\Auth\AuthController::class, 'me'])->middleware('auth')->name('me');
});

// ===================================================================
// ROTAS PROTEGIDAS (REQUEREM AUTENTICAÇÃO)
// ===================================================================

Route::middleware(['auth'])->group(function () {
    
    // ===== ROTAS WEB - INTERFACE =====
    
    // Home (dashboard principal)
    Route::get('/home', function () {
        return view('home');
    })->name('home');
    
    // ===== ROTAS ADMINISTRATIVAS - INTERFACE =====
    
    // Prefixo para todas as rotas administrativas
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Usuários
        Route::resource('usuarios', \App\Http\Controllers\Web\Administracao\Usuarios\UsuariosController::class);
        
        // Papéis (Roles)
        Route::resource('papeis', \App\Http\Controllers\Web\Administracao\Roles\RolesController::class);
        
        // Permissões
        Route::resource('permissoes', \App\Http\Controllers\Web\Administracao\Permissions\PermissionsController::class);
        
        // Active Directory
        Route::prefix('active-directory')->name('active-directory.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Administracao\ActiveDirectory\ActiveDirectoryController::class, 'index'])->name('index');
            Route::get('/config', [\App\Http\Controllers\Web\Administracao\ActiveDirectory\ActiveDirectoryController::class, 'config'])->name('config');
            Route::get('/sync', [\App\Http\Controllers\Web\Administracao\ActiveDirectory\ActiveDirectoryController::class, 'sync'])->name('sync');
        });
    });
    
    // ===== ROTAS API - DADOS =====
    
    // API para dados do usuário (session-based) - compatibilidade com componentes Vue
    Route::get('/user-data', function () {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return response()->json([
                'id' => \Illuminate\Support\Facades\Auth::user()->id,
                'name' => \Illuminate\Support\Facades\Auth::user()->name,
                'email' => \Illuminate\Support\Facades\Auth::user()->email,
                'authenticated' => true
            ]);
        }
        
        return response()->json(['authenticated' => false], 401);
    });
    
    // ===== ROTAS API ADMINISTRATIVAS =====
    
    // Prefixo para todas as APIs administrativas
    Route::prefix('api/administracao')->name('api.administracao.')->group(function () {
        
        // Usuários
        Route::apiResource('usuarios', \App\Http\Controllers\Api\Administracao\Usuarios\UsuariosController::class);
        
        // Papéis (Roles)
        Route::apiResource('papeis', \App\Http\Controllers\Api\Administracao\Roles\RolesController::class);
        Route::post('papeis/{id}/permissions', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'updatePermissions'])->name('papeis.permissions.update');
        Route::get('papeis/{id}/permissions', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'getPermissions'])->name('papeis.permissions.get');
        Route::get('papeis/{id}/permissions/available', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'getAvailablePermissions'])->name('papeis.permissions.available');
        // Usuários por papel
        Route::get('papeis/{id}/users', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'getUsers'])->name('papeis.users.get');
        Route::post('papeis/{id}/users', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'addUser'])->name('papeis.users.add');
        Route::delete('papeis/{id}/users/{userId}', [\App\Http\Controllers\Api\Administracao\Roles\RolesController::class, 'removeUser'])->name('papeis.users.remove');
        
        // Permissões
        Route::apiResource('permissoes', \App\Http\Controllers\Api\Administracao\PermissionsController::class);
        Route::post('permissoes/{permissionId}/roles/{roleId}/attach', [\App\Http\Controllers\Api\Administracao\PermissionsController::class, 'attachRole'])->name('permissoes.roles.attach');
        Route::delete('permissoes/{permissionId}/roles/{roleId}/detach', [\App\Http\Controllers\Api\Administracao\PermissionsController::class, 'detachRole'])->name('permissoes.roles.detach');
        Route::get('permissoes/role/{roleId}', [\App\Http\Controllers\Api\Administracao\PermissionsController::class, 'getByRole'])->name('permissoes.by-role');
        Route::post('permissoes/role/{roleId}/sync', [\App\Http\Controllers\Api\Administracao\PermissionsController::class, 'syncRolePermissions'])->name('permissoes.roles.sync');
        
        // Busca Global
        Route::get('busca-global', [\App\Http\Controllers\Api\Administracao\BuscaGlobalController::class, 'buscar'])->name('busca-global');
        
        // Active Directory
        Route::prefix('active-directory')->name('active-directory.')->group(function () {
            // Configurações
            Route::get('config', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\ConfigController::class, 'index'])->name('config.index');
            Route::post('config', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\ConfigController::class, 'store'])->name('config.store');
            Route::get('config/test', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\ConfigController::class, 'test'])->name('config.test');
            
            // Sincronização
            Route::post('sync', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\SyncController::class, 'sync'])->name('sync.execute');
            Route::get('sync/status', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\SyncController::class, 'status'])->name('sync.status');
            Route::get('sync/test-connection', [\App\Http\Controllers\Api\Administracao\ActiveDirectory\SyncController::class, 'testConnection'])->name('sync.test-connection');
        });
    });
    
    // ===== ROTAS TEMPORÁRIAS (REDIRECIONAM PARA HOME) =====
    
    // Rotas que existiam no sistema antigo - redirecionam para home
    Route::get('/andamento-projeto', function() { 
        return redirect('/home'); 
    })->name('andamento-projeto.index');

    Route::get('/planilha-prototipo', function() { 
        return redirect('/home'); 
    })->name('planilha-prototipo');
});