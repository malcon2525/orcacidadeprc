<?php

namespace App\Models\Administracao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Model para Papéis do Sistema
 * 
 * Representa a tabela 'roles' com relacionamentos
 * para usuários e permissões.
 */
class Role extends Model
{
    use HasFactory;

    /**
     * Nome da tabela
     */
    protected $table = 'roles';

    /**
     * Atributos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
    ];

    /**
     * Casting de atributos
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Papel tem muitas permissões (N:N)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->withTimestamps();
    }

    /**
     * Relacionamento: Papel tem muitos usuários (N:N)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles')
                    ->withTimestamps();
    }

    /**
     * Scope para papéis ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para papéis inativos
     */
    public function scopeInativo($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope para filtrar por nome
     */
    public function scopePorNome($query, $nome)
    {
        return $query->where('name', 'like', "%{$nome}%")
                     ->orWhere('display_name', 'like', "%{$nome}%");
    }

    /**
     * Accessor para texto do status
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Ativo' : 'Inativo';
    }

    /**
     * Verifica se o papel está ativo
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Conta o número de usuários neste papel
     * (Usar withCount() no query para performance)
     */
    public function getUsersCountAttribute()
    {
        return $this->users_count ?? $this->users()->count();
    }

    /**
     * Conta o número de permissões neste papel
     * (Usar withCount() no query para performance)
     */
    public function getPermissionsCountAttribute()
    {
        return $this->permissions_count ?? $this->permissions()->count();
    }

    /**
     * Conta o número de permissões ativas neste papel
     * (Usar withCount() no query para performance)
     */
    public function getActivePermissionsCountAttribute()
    {
        return $this->active_permissions_count ?? $this->permissions()->where('is_active', true)->count();
    }
} 