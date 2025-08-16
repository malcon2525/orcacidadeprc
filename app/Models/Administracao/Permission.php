<?php

namespace App\Models\Administracao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relacionamento many-to-many com roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * Relacionamento many-to-many com users através de roles
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_permissions', 'permission_id', 'role_id')
                    ->join('user_roles', 'roles.id', '=', 'user_roles.role_id')
                    ->join('users', 'user_roles.user_id', '=', 'users.id')
                    ->select('users.*')
                    ->distinct();
    }

    /**
     * Verifica se a permissão está ativa
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Verifica se a permissão está atribuída a um papel específico
     */
    public function isAssignedToRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Verifica se a permissão está atribuída a pelo menos um dos papéis especificados
     */
    public function isAssignedToAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Escopo para permissões ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Escopo para permissões inativas
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Escopo para permissões por nome
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Escopo para permissões que contêm texto no nome
     */
    public function scopeNameContains($query, string $text)
    {
        return $query->where('name', 'like', "%{$text}%");
    }
}
