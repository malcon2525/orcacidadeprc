<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ActiveDirectoryService
{
    private $connection;
    private $host;
    private $port;
    private $baseDn;
    private $username;
    private $password;

    public function __construct()
    {
        // Verificar se AD está habilitado
        if (!config('adldap.connections.default.enabled', false)) {
            throw new \Exception('Active Directory não está habilitado');
        }

        // Carregar configurações
        $this->host = config('adldap.connections.default.settings.hosts.0');
        $this->port = config('adldap.connections.default.settings.port');
        $this->baseDn = config('adldap.connections.default.settings.base_dn');
        $this->username = config('adldap.connections.default.settings.username');
        $this->password = config('adldap.connections.default.settings.password');

        if (empty($this->host) || empty($this->baseDn) || empty($this->username) || empty($this->password)) {
            throw new \Exception('Configurações do AD incompletas');
        }
    }

    /**
     * Conectar ao Active Directory
     */
    public function connect()
    {
        try {
            Log::info('Tentando conectar ao AD', [
                'host' => $this->host,
                'port' => $this->port,
                'base_dn' => $this->baseDn,
                'username' => $this->username
            ]);

            // Conectar ao LDAP
            $this->connection = ldap_connect($this->host, $this->port);
            
            if (!$this->connection) {
                throw new \Exception('Não foi possível conectar ao servidor LDAP');
            }

            // Configurar opções LDAP
            ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);

            // Autenticar
            $bind = ldap_bind($this->connection, $this->username, $this->password);
            
            if (!$bind) {
                throw new \Exception('Falha na autenticação LDAP: ' . ldap_error($this->connection));
            }

            Log::info('Conexão com AD estabelecida com sucesso');
            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao conectar com AD', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Buscar todos os usuários do AD
     */
    public function getUsers()
    {
        try {
            if (!$this->connection) {
                $this->connect();
            }

            // Buscar usuários
            $filter = '(&(objectClass=user)(sAMAccountName=*))';
            $search = ldap_search($this->connection, $this->baseDn, $filter);
            
            if (!$search) {
                throw new \Exception('Erro na busca LDAP: ' . ldap_error($this->connection));
            }

            $entries = ldap_get_entries($this->connection, $search);
            
            $adUsers = [];
            for ($i = 0; $i < $entries['count']; $i++) {
                $entry = $entries[$i];
                
                try {
                    $adUsers[] = [
                        'id' => $entry['dn'],
                        'name' => $entry['displayname'][0] ?? $entry['name'][0] ?? 'Usuário sem nome',
                        'email' => $entry['mail'][0] ?? '',
                        'username' => $entry['samaccountname'][0] ?? ''
                    ];
                } catch (\Exception $userError) {
                    Log::warning('Erro ao processar usuário individual', [
                        'user_dn' => $entry['dn'],
                        'error' => $userError->getMessage()
                    ]);
                    continue;
                }
            }

            Log::info('Usuários encontrados no AD', ['count' => count($adUsers)]);
            return $adUsers;

        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuários do AD', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Testar conexão com AD
     */
    public function testConnection()
    {
        try {
            $this->connect();
            $users = $this->getUsers();
            
            return [
                'success' => true,
                'message' => 'Conexão com AD estabelecida com sucesso',
                'users_count' => count($users),
                'server_info' => [
                    'host' => $this->host,
                    'port' => $this->port,
                    'base_dn' => $this->baseDn
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro na conexão: ' . $e->getMessage(),
                'users_count' => 0
            ];
        }
    }

    /**
     * Buscar usuário por email
     */
    public function findUserByEmail($email)
    {
        try {
            if (!$this->connection) {
                $this->connect();
            }

            // Buscar usuário específico por email
            $filter = "(&(objectClass=user)(mail={$email}))";
            $search = ldap_search($this->connection, $this->baseDn, $filter);
            
            if (!$search) {
                throw new \Exception('Erro na busca LDAP: ' . ldap_error($this->connection));
            }

            $entries = ldap_get_entries($this->connection, $search);
            
            if ($entries['count'] === 0) {
                return null;
            }

            $entry = $entries[0];
            
            return [
                'id' => $entry['dn'],
                'name' => $entry['displayname'][0] ?? $entry['name'][0] ?? 'Usuário sem nome',
                'email' => $entry['mail'][0] ?? '',
                'username' => $entry['samaccountname'][0] ?? ''
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuário por email no AD', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Autenticar usuário no AD
     */
    public function authenticateUser($username, $password)
    {
        try {
            // Extrair domínio do base_dn (ex: OU=Empregados,DC=prcidade,DC=br -> prcidade.br)
            $baseDn = config('adldap.connections.default.settings.base_dn');
            $domain = $this->extractDomainFromBaseDn($baseDn);
            
            // Testar diferentes formatos de autenticação
            $formats = [
                $username . '@' . $domain,
                $username,
                $username . '@' . 'paranacidade.org.br'
            ];
            
            Log::info('Testando formatos de autenticação AD', [
                'username' => $username,
                'domain' => $domain,
                'formats' => $formats
            ]);
            
            foreach ($formats as $index => $userDn) {
                try {
                    Log::info("Tentativa " . ($index + 1) . ": " . $userDn);
                    
                    $testConnection = ldap_connect($this->host, $this->port);
                    
                    if (!$testConnection) {
                        throw new \Exception('Não foi possível conectar ao servidor LDAP para autenticação');
                    }

                    ldap_set_option($testConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($testConnection, LDAP_OPT_REFERRALS, 0);

                    // Tentar bind com as credenciais do usuário
                    $bind = ldap_bind($testConnection, $userDn, $password);
                    
                    ldap_close($testConnection);
                    
                    if ($bind) {
                        Log::info("Autenticação bem-sucedida com formato: " . $userDn);
                        return true;
                    }
                    
                } catch (\Exception $e) {
                    Log::warning("Falha na tentativa " . ($index + 1) . " com " . $userDn . ": " . $e->getMessage());
                    continue;
                }
            }
            
            Log::error('Todas as tentativas de autenticação falharam', [
                'username' => $username,
                'formats_tested' => $formats
            ]);
            
            return false;

        } catch (\Exception $e) {
            Log::error('Erro na autenticação de usuário no AD', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Extrair domínio do base_dn
     */
    private function extractDomainFromBaseDn($baseDn)
    {
        // Converter OU=Empregados,DC=prcidade,DC=br para prcidade.br
        $parts = explode(',', $baseDn);
        $domainParts = [];
        
        foreach ($parts as $part) {
            if (strpos($part, 'dc=') === 0) {
                $domainParts[] = substr($part, 3);
            }
        }
        
        $domain = implode('.', $domainParts);
        
        Log::info('Domínio extraído do base_dn', [
            'base_dn' => $baseDn,
            'domain' => $domain
        ]);
        
        return $domain;
    }

    /**
     * Destrutor para fechar conexão
     */
    public function __destruct()
    {
        if ($this->connection) {
            ldap_unbind($this->connection);
        }
    }
} 