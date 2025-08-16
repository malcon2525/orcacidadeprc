<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Administracao\Role;

/**
 * Model para Usuários do Sistema
 *
 * Representa a tabela 'users' com suporte a autenticação
 * híbrida (local + Active Directory) e gestão de papéis.
 */
class User extends Authenticatable implements JWTSubject      
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'is_active',
        'last_login_at',
        'ad_user_id',
        'ad_domain',
        'ad_sync_at',
        'login_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'ad_sync_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Verifica se o usuário está ativo
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Atualiza último login
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Scope para usuários ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para usuários inativos
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope para usuários por tipo de login
     */
    public function scopeByLoginType($query, $type)
    {
        return $query->where('login_type', $type);
    }

    /**
     * Relacionamento: Usuário tem muitos papéis (N:N)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withTimestamps();
    }

    /**
     * Verifica se o usuário tem um papel específico
     */
    public function hasRole($roleName)
    {
        return $this->roles()
                    ->where('name', $roleName)
                    ->where('is_active', true)
                    ->exists();
    }

    /**
     * Verifica se o usuário tem uma permissão específica
     */
    public function hasPermission($permissionName)
    {
        return $this->roles()
                    ->where('is_active', true)
                    ->whereHas('permissions', function($query) use ($permissionName) {
                        $query->where('name', $permissionName)
                              ->where('is_active', true);
                    })
                    ->exists();
    }

    /**
     * Retorna todas as permissões consolidadas do usuário
     */
    public function getAllPermissions()
    {
        $permissions = collect();
        
        foreach ($this->roles()->where('is_active', true)->get() as $role) {
            foreach ($role->permissions()->where('is_active', true)->get() as $permission) {
                if (!$permissions->contains('name', $permission->name)) {
                    $permissions->push($permission);
                }
            }
        }
        
        return $permissions;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
