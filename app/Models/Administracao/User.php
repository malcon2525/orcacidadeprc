<?php

namespace App\Models\Administracao;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'ad_sync_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relacionamento many-to-many com roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * Relacionamento many-to-many com permissions através de roles
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_roles', 'user_id', 'role_id')
                    ->join('role_permissions', 'user_roles.role_id', '=', 'role_permissions.role_id')
                    ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                    ->select('permissions.*')
                    ->distinct();
    }

    /**
     * Verifica se o usuário tem um papel específico
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Verifica se o usuário tem uma permissão específica
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->roles()
                    ->whereHas('permissions', function ($query) use ($permissionName) {
                        $query->where('name', $permissionName);
                    })
                    ->exists();
    }

    /**
     * Verifica se o usuário tem pelo menos um dos papéis especificados
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Verifica se o usuário tem pelo menos uma das permissões especificadas
     */
    public function hasAnyPermission(array $permissionNames): bool
    {
        return $this->roles()
                    ->whereHas('permissions', function ($query) use ($permissionNames) {
                        $query->whereIn('name', $permissionNames);
                    })
                    ->exists();
    }

    /**
     * Verifica se o usuário é super admin (tem papel 'super')
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super');
    }

    /**
     * Verifica se o usuário está ativo
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Verifica se o usuário pode fazer login
     */
    public function canLogin(): bool
    {
        return $this->isActive();
    }

    /**
     * Atualiza último login
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Verifica se o usuário é do tipo AD
     */
    public function isADUser(): bool
    {
        return $this->login_type === 'ad';
    }

    /**
     * Verifica se o usuário é do tipo local
     */
    public function isLocalUser(): bool
    {
        return $this->login_type === 'local';
    }

    // ===== NOVOS RELACIONAMENTOS PARA APROVAÇÃO DE CADASTROS =====

    /**
     * Relacionamento com Município (através das entidades orçamentárias)
     * Retorna o primeiro município das entidades vinculadas
     */
    public function municipio(): ?object
    {
        return $this->entidadesOrcamentarias()->first()?->municipio;
    }

    /**
     * Relacionamento many-to-many com Entidades Orçamentárias
     */
    public function entidadesOrcamentarias(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria::class,
            'user_entidades_orcamentarias',
            'user_id',
            'entidade_orcamentaria_id'
        )
        ->withPivot('ativo', 'data_vinculacao', 'vinculado_por_user_id')
        ->withTimestamps()
        ->wherePivot('ativo', true);
    }

    /**
     * Relacionamento many-to-many com Entidades Orçamentárias (incluindo inativas)
     */
    public function todasEntidadesOrcamentarias(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria::class,
            'user_entidades_orcamentarias',
            'user_id',
            'entidade_orcamentaria_id'
        )
        ->withPivot('ativo', 'data_vinculacao', 'vinculado_por_user_id')
        ->withTimestamps();
    }

    /**
     * Relacionamento com Solicitações de Cadastro
     */
    public function solicitacoesCadastro(): HasMany
    {
        return $this->hasMany(SolicitacaoCadastro::class, 'user_id');
    }

    /**
     * Relacionamento com Solicitações de Cadastro aprovadas por este usuário
     */
    public function solicitacoesAprovadas(): HasMany
    {
        return $this->hasMany(SolicitacaoCadastro::class, 'aprovado_por_user_id');
    }

    /**
     * Relacionamento com vinculações de usuários a entidades feitas por este usuário
     */
    public function vinculacoesFeitas(): HasMany
    {
        return $this->hasMany(
            \App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria::class,
            'vinculado_por_user_id',
            'id'
        )->join('user_entidades_orcamentarias', 'entidades_orcamentarias.id', '=', 'user_entidades_orcamentarias.entidade_orcamentaria_id');
    }

    // ===== MÉTODOS AUXILIARES PARA APROVAÇÃO DE CADASTROS =====

    /**
     * Verifica se o usuário pode aprovar cadastros
     */
    public function podeAprovarCadastros(): bool
    {
        return $this->isSuperAdmin() || $this->hasPermission('aprovar-cadastros');
    }

    /**
     * Verifica se o usuário está vinculado a uma entidade específica
     */
    public function estaVinculadoAEntidade(int $entidadeId): bool
    {
        return $this->entidadesOrcamentarias()->where('entidade_orcamentaria_id', $entidadeId)->exists();
    }

    /**
     * Retorna as entidades ativas do usuário
     */
    public function getEntidadesAtivasAttribute()
    {
        return $this->entidadesOrcamentarias;
    }

    /**
     * Retorna o nome do município do usuário
     */
    public function getNomeMunicipioAttribute(): ?string
    {
        return $this->municipio?->nome;
    }

    /**
     * Scope para usuários vinculados a uma entidade específica
     */
    public function scopeVinculadosAEntidade($query, int $entidadeId)
    {
        return $query->whereHas('entidadesOrcamentarias', function ($q) use ($entidadeId) {
            $q->where('entidade_orcamentaria_id', $entidadeId);
        });
    }

    /**
     * Scope para usuários de um município específico (através das entidades)
     */
    public function scopeDoMunicipio($query, int $municipioId)
    {
        return $query->whereHas('entidadesOrcamentarias.municipio', function ($q) use ($municipioId) {
            $q->where('id', $municipioId);
        });
    }
}
