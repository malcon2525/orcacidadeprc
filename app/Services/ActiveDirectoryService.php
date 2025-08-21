<?php

namespace App\Services;

use App\Services\Logging\ActiveDirectoryLogService;

class ActiveDirectoryService
{
    private $connection;
    private $host;
    private $port;
    private $baseDn;
    private $username;
    private $password;
    protected $logger;

    public function __construct(ActiveDirectoryLogService $logger)
    {
        $this->logger = $logger;
        
        // Carregar configurações (não falhar se não estiverem configuradas)
        $this->host = config('adldap.connections.default.settings.hosts.0', 'localhost');
        $this->port = config('adldap.connections.default.settings.port', 389);
        $this->baseDn = config('adldap.connections.default.settings.base_dn', 'DC=exemplo,DC=com');
        $this->username = config('adldap.connections.default.settings.username', '');
        $this->password = config('adldap.connections.default.settings.password', '');
    }

    /**
     * Conectar ao Active Directory
     */
    public function connect()
    {
        try {
            // Verificar se AD está habilitado
            if (!config('adldap.connections.default.enabled', false)) {
                throw new \Exception('Active Directory não está habilitado');
            }

            // Verificar se as configurações estão completas
            if (empty($this->host) || empty($this->baseDn) || empty($this->username) || empty($this->password)) {
                throw new \Exception('Configurações do AD incompletas');
            }

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

            // Log apenas se for uma operação crítica ou se falhar
            return true;

        } catch (\Exception $e) {
            // Log de falha na conexão (apenas erros)
            $this->logger->falhaConexao($this->host, $this->port, $e->getMessage(), [
                'base_dn' => $this->baseDn,
                'username' => $this->username
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

            // Buscar usuários (sem log de início)
            $filtro = '(&(objectClass=user)(sAMAccountName=*))';
            $search = ldap_search($this->connection, $this->baseDn, $filtro);
            
            if (!$search) {
                throw new \Exception('Erro na busca LDAP: ' . ldap_error($this->connection));
            }

            $entries = ldap_get_entries($this->connection, $search);
            
            $adUsers = [];
            for ($i = 0; $i < $entries['count']; $i++) {
                $entry = $entries[$i];
                
                // Processar entrada do usuário
                $adUsers[] = $this->processUserEntry($entry);
            }

            // Log apenas se for uma operação crítica ou se falhar
            return $adUsers;

        } catch (\Exception $e) {
            // Log apenas em caso de erro
            $this->logger->erroCritico('BUSCA_USUARIOS_AD', $e->getMessage(), [
                'base_dn' => $this->baseDn,
                'filtro' => $filtro ?? 'N/A'
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
            $this->logger->erroCritico('BUSCA_USUARIO_POR_EMAIL', $e->getMessage(), [
                'email' => $email
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
            
            $this->logger->tentativaAutenticacaoAD($username, $formats);
            
            foreach ($formats as $index => $userDn) {
                try {
                    $this->logger->tentativaAutenticacaoAD($username, $formats, $index + 1);
                    
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
                        $this->logger->sucessoAutenticacaoAD($username, $userDn);
                        return true;
                    }
                    
                } catch (\Exception $e) {
                    $this->logger->falhaAutenticacaoAD($username, $userDn, $e->getMessage());
                    continue;
                }
            }
            
            $this->logger->falhaAutenticacaoAD($username, $formats, 'Todas as tentativas falharam');
            
            return false;

        } catch (\Exception $e) {
            $this->logger->erroCritico('AUTENTICACAO_USUARIO_AD', $e->getMessage(), [
                'username' => $username
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
        
        return $domain;
    }

    /**
     * Processar entrada do usuário do AD
     */
    private function processUserEntry($entry)
    {
        try {
            return [
                'id' => $entry['dn'],
                'name' => $entry['displayname'][0] ?? $entry['name'][0] ?? 'Usuário sem nome',
                'email' => $entry['mail'][0] ?? '',
                'username' => $entry['samaccountname'][0] ?? ''
            ];
        } catch (\Exception $userError) {
            $this->logger->erroCritico('PROCESSAMENTO_USUARIO_INDIVIDUAL', $userError->getMessage(), [
                'user_dn' => $entry['dn']
            ]);
            return null; // Retorna null para indicar que o usuário não pôde ser processado
        }
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
