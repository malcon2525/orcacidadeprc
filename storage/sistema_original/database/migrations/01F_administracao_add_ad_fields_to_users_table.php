<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('email'); // Nullable inicialmente
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('password');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'ad_user_id')) {
                $table->string('ad_user_id')->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'ad_domain')) {
                $table->string('ad_domain')->nullable()->after('ad_user_id');
            }
            if (!Schema::hasColumn('users', 'ad_sync_at')) {
                $table->timestamp('ad_sync_at')->nullable()->after('ad_domain');
            }
            if (!Schema::hasColumn('users', 'login_type')) {
                $table->enum('login_type', ['local', 'ad', 'hybrid'])->default('local')->after('ad_sync_at');
            }
        });

        // Preencher username para TODOS os usuários existentes que não têm
        DB::table('users')
            ->whereNull('username')
            ->update([
                'username' => DB::raw("CONCAT('user_', id)"), // user_1, user_2, etc.
                'is_active' => true,
                'login_type' => 'local'
            ]);
            
        // Atualizar especificamente o usuário adm
        DB::table('users')
            ->where('email', 'adm@adm.com.br')
            ->update([
                'username' => 'adm',
                'is_active' => true,
                'login_type' => 'local'
            ]);

        // Tornar username obrigatório após preencher dados
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable(false)->change();
                // Verificar se o índice único já existe
                $indexExists = collect(DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_username_unique'"))->isNotEmpty();
                if (!$indexExists) {
                    $table->unique('username');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn([
                'username',
                'is_active',
                'last_login_at',
                'ad_user_id',
                'ad_domain',
                'ad_sync_at',
                'login_type'
            ]);
        });
    }
};
