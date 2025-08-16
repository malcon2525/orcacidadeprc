<?php

namespace App\Models\Administracao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model para Permissões do Sistema
 * 
 * Representa a tabela 'permissions' vinculada a papéis.
 */
class Permission extends Model
{
    use HasFactory;

    /**
     * Nome da tabela
     */
    protected $table = 'permissions';

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
     * Relacionamento: Permissão tem muitos papéis (N:N)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
                    ->withTimestamps();
    }

    /**
     * Scope para permissões ativas
     */
    public function scopeAtivo($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para permissões inativas
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
} 