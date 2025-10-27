<?php
// Configuração de ambiente
// Carregar variáveis do arquivo .env se existir
if (file_exists(__DIR__ . '/../.env')) {
    $envFile = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        if (strpos($line, '#') === 0 || strpos($line, '=') === false) continue; // Ignorar comentários e linhas sem =
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $_ENV[$key] = $value;
        putenv("$key=$value"); // Também definir como variável de ambiente
    }
}

$environment = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'development';

// Configurações por ambiente
if ($environment === 'production') {
    // Configurações de PRODUÇÃO
    $host = 'localhost'; // Host padrão para produção

    // Credenciais específicas por banco (conforme fornecidas)
    $db_credentials = [
        'ouvidoria' => [
            'host' => 'localhost',
            'user' => 'bdsolatend',
            'pass' => 'opqwioihjOHUQHOQWNNA234',
            'dbname' => 'bdsolicita_atendimento'  // AGORA - Corrigido para nome real do banco
        ],
        'ead' => [
            'host' => 'localhost',
            'user' => 'eaduseroot',
            'pass' => 'OPHFQOIfOHOUHOou-039-',
            'dbname' => 'dbead'  // Corrigido para nome real do banco
        ],
        'processo_seletivo' => [
            'host' => 'localhost',
            'user' => 'pseluserdb',
            'pass' => 'gpoaGGWOIJW5874ajds123478',
            'dbname' => 'pseldb'  // Corrigido para nome real do banco
        ],
        'secretaria' => [
            'host' => 'localhost',
            'user' => 'dbsecretacaduser',
            'pass' => 'agkljUHP79654AJjosh27Y616',
            'dbname' => 'dbsecretacad'  // Corrigido para nome real do banco
        ],
        'financeiro' => [
            'host' => 'localhost',
            'user' => 'userfinidb',
            'pass' => 'AJHPFGiuhpuwh093876524yr',
            'dbname' => 'fini'  // Corrigido para nome real do banco
        ],
        'exaluno' => [
            'host' => 'localhost',
            'user' => 'bdsolatend', // Mesmo usuário da Ouvidoria
            'pass' => 'opqwioihjOHUQHOQWNNA234', // Mesma senha da Ouvidoria
            'dbname' => 'bdsolicita_atendimento'  // Corrigido para nome real do banco
        ],
        'portal_diretoria' => [
            'host' => 'localhost', // Host confirmado funcionando
            'user' => 'usergprotoc',
            'pass' => '8092QNSANSnjlaskn0u2Jjhoiq02',
            'dbname' => 'dbgproto'
        ]
    ];

    // Aplicar configurações específicas
    $host = $db_credentials['portal_diretoria']['host']; // Usar host do portal diretoria como padrão
    $user = $db_credentials['portal_diretoria']['user'];
    $password = $db_credentials['portal_diretoria']['pass'];

    // Bancos de dados de produção
    $dbname_ouvidoria = $db_credentials['ouvidoria']['dbname'];
    $dbname_ead = $db_credentials['ead']['dbname'];
    $dbname_processo_seletivo = $db_credentials['processo_seletivo']['dbname'];
    $dbname_secretaria = $db_credentials['secretaria']['dbname'];
    $dbname_financeiro = $db_credentials['financeiro']['dbname'];
    $dbname_exaluno = $db_credentials['exaluno']['dbname'];

    // Banco do portal da diretoria (notificações)
    $dbname_portal_diretoria = $db_credentials['portal_diretoria']['dbname'];

} else {
    // Configurações de DESENVOLVIMENTO (localhost)
    $host = 'localhost';
    $user = 'root'; // Usuário padrão do XAMPP
    $password = ''; // Senha padrão vazia

    // Bancos de dados de desenvolvimento
    $dbname_ouvidoria = 'portal_ouvidoria';
    $dbname_ead = 'portal_ead';
    $dbname_processo_seletivo = 'portal_processo_seletivo';
    $dbname_secretaria = 'portal_secretaria_academica';
    $dbname_financeiro = 'portal_financeiro';
    $dbname_exaluno = 'portal_exaluno';

    // Banco do portal da diretoria (notificações)
    $dbname_portal_diretoria = 'portal_diretoria';
}

// Constantes para nomes de tabelas e colunas - AJUSTE CONFORME SEU BANCO DE DADOS

// Ouvidoria
define('TABLE_OUVIDORIA', 'chamados');
define('COL_ID_OUVIDORIA', 'id');
define('COL_STATUS_OUVIDORIA', 'status');
define('COL_DATA_ABERTURA_OUVIDORIA', 'data_abertura');
define('COL_DATA_FECHAMENTO_OUVIDORIA', 'data_encerramento');
define('COL_TIPO_OUVIDORIA', 'manifestacao');
define('COL_DESCRICAO_OUVIDORIA', 'descricao');

// EAD
define('TABLE_EAD', 'chamados');
define('COL_ID_EAD', 'id');
define('COL_STATUS_EAD', 'status');
define('COL_DATA_ABERTURA_EAD', 'data_abertura');
define('COL_DATA_FECHAMENTO_EAD', 'data_encerramento');
define('COL_SERVICO_EAD', 'categoria');
define('COL_DESCRICAO_EAD', 'descricao');

// Processo Seletivo
define('TABLE_PROCESSO', 'chamados');
define('COL_ID_PROCESSO', 'id');
define('COL_STATUS_PROCESSO', 'status');
define('COL_DATA_ABERTURA_PROCESSO', 'data_abertura');
define('COL_DATA_FECHAMENTO_PROCESSO', 'data_resposta');
define('COL_SERVICO_PROCESSO', 'categoria');
define('COL_DESCRICAO_PROCESSO', 'descricao');

// Secretaria Acadêmica
define('TABLE_SECRETARIA', 'chamados');
define('COL_ID_SECRETARIA', 'id');
define('COL_STATUS_SECRETARIA', 'status');
define('COL_DATA_ABERTURA_SECRETARIA', 'data_abertura');
define('COL_DATA_CONCLUSAO_SECRETARIA', 'data_encerramento');
define('COL_SERVICO_SECRETARIA', 'categoria');
define('COL_DESCRICAO_SECRETARIA', 'descricao');

// Financeiro
define('TABLE_FINANCEIRO', 'chamados');
define('COL_ID_FINANCEIRO', 'id');
define('COL_STATUS_FINANCEIRO', 'status');
define('COL_DATA_ABERTURA_FINANCEIRO', 'data_abertura');
define('COL_DATA_FECHAMENTO_FINANCEIRO', 'data_encerramento');
define('COL_SERVICO_FINANCEIRO', 'categoria');
define('COL_DESCRICAO_FINANCEIRO', 'descricao');

// Ex-Aluno
define('TABLE_EXALUNO', $environment === 'production' ? 'doc_solicitacoes_documentos' : 'chamados');
define('COL_ID_EXALUNO', 'id');
define('COL_STATUS_EXALUNO', 'status');
define('COL_DATA_ABERTURA_EXALUNO', $environment === 'production' ? 'data_solicitacao' : 'data_abertura');
define('COL_DATA_FECHAMENTO_EXALUNO', 'data_encerramento');
define('COL_SERVICO_EXALUNO', $environment === 'production' ? 'documento_solicitado' : 'manifestacao');
define('COL_DESCRICAO_EXALUNO', $environment === 'production' ? 'mensagem' : 'descricao');

// Função para conectar a um DB específico (usando credenciais específicas)
if (!function_exists('connectDB')) {
    function connectDB($dbname) {
        global $db_credentials, $environment;

        if ($environment === 'production') {
            // Encontrar as credenciais do banco específico
            $credenciais = null;
            foreach ($db_credentials as $portal => $creds) {
                if ($creds['dbname'] === $dbname) {
                    $credenciais = $creds;
                    break;
                }
            }

            if (!$credenciais) {
                throw new Exception("Credenciais não encontradas para o banco: $dbname");
            }

            try {
                $pdo = new PDO("mysql:host={$credenciais['host']};dbname={$credenciais['dbname']};charset=utf8",
                              $credenciais['user'], $credenciais['pass']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (PDOException $e) {
                throw new Exception("Erro ao conectar ao banco '$dbname': " . $e->getMessage());
            }
        } else {
            // Desenvolvimento - usar credenciais globais
            global $host, $user, $password;
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (PDOException $e) {
                throw new Exception("Banco de dados '$dbname' não encontrado ou inacessível");
            }
        }
    }
}

// Função para conectar ao banco do portal da diretoria (notificações)
if (!function_exists('connectPortalDiretoria')) {
    function connectPortalDiretoria() {
        global $environment, $db_credentials;

        if ($environment === 'production' && isset($db_credentials['portal_diretoria'])) {
            // Usar credenciais específicas de produção
            $config = $db_credentials['portal_diretoria'];
            $host = $config['host'];
            $user = $config['user'];
            $pass = $config['pass'];
            $dbname = $config['dbname'];
        } else {
            // Credenciais de desenvolvimento
            $host = 'localhost';
            $user = 'root';
            $pass = '';
            $dbname = 'portal_diretoria';
        }

        try {
            $conn = new mysqli($host, $user, $pass, $dbname);
            if ($conn->connect_error) {
                throw new Exception("Erro de conexão com portal_diretoria: " . $conn->connect_error);
            }
            return $conn;
        } catch (Exception $e) {
            throw new Exception("Banco de dados portal_diretoria não encontrado ou inacessível: " . $e->getMessage());
        }
    }
}

// Lista de bancos para pesquisar
$bancos = [
    [
        'portal' => 'Portal EAD',
        'dsn' => 'mysql:host=localhost;dbname=dbead;charset=utf8',
        'user' => 'eaduseroot',
        'pass' => 'OPHFQOIfOHOUHOou-039-',
        'tabela' => 'chamados'
    ],
    [
        'portal' => 'Portal Financeiro',
        'dsn' => 'mysql:host=localhost;dbname=fini;charset=utf8',
        'user' => 'userfinidb',
        'pass' => 'AJHPFGiuhpuwh093876524yr',
        'tabela' => 'chamados'
    ],
    [
        'portal' => 'Portal Seletivo',
        'dsn' => 'mysql:host=localhost;dbname=portal_processo_seletivo;charset=utf8',
        'user' => 'root',
        'pass' => '',
        'tabela' => 'chamados'
    ],
    [
        'portal' => 'Portal Documentos',
        'dsn' => 'mysql:host=localhost;dbname=bdsolicita_atendimento;charset=utf8',
        'user' => 'bdsolatend',
        'pass' => 'opqwioihjOHUQHOQWNNA234',
        'tabela' => 'doc_solicitacoes_documentos'
    ],
    // Adicione outros portais aqui
    // [
    // 'portal' => 'Portal Tecnologia',
    // 'dsn' => 'mysql:host=localhost;dbname=portal_tecnologia;charset=utf8',
    // 'user' => 'root',
    // 'pass' => '',
    // 'tabela' => 'chamados'
    // ],
];

// Função helper para mapear nomes de bancos baseado no ambiente
function getProductionDatabaseName($development_name) {
    global $environment;
    
    if ($environment !== 'production') {
        return $development_name;
    }
    
    // Mapeamento desenvolvimento → produção
    $database_mapping = [
        // Mapeamento com prefixo portal_
        'portal_ouvidoria' => 'bdsolicita_atendimento',
        'portal_ead' => 'dbead',
        'portal_processo_seletivo' => 'pseldb',
        'portal_secretaria_academica' => 'dbsecretacad',
        'portal_financeiro' => 'fini',
        'portal_exaluno' => 'bdsolicita_atendimento',
        'portal_diretoria' => 'dbgproto',
        
        // Mapeamento direto (sem prefixo)
        'ouvidoria' => 'bdsolicita_atendimento',
        'ead' => 'dbead',
        'processo_seletivo' => 'pseldb',
        'secretaria' => 'dbsecretacad',
        'financeiro' => 'fini',
        'exaluno' => 'bdsolicita_atendimento'
    ];
    
    return $database_mapping[$development_name] ?? $development_name;
}

// Função para conectar a qualquer portal (compatível com ambiente)
if (!function_exists('connectDBEnvironment')) {
    function connectDBEnvironment($portal_name) {
        global $db_credentials, $environment;
        
        // Mapeamento simples de nomes de bancos para desenvolvimento
        $dev_db_mapping = [
            'ouvidoria' => 'portal_ouvidoria',
            'ead' => 'portal_ead',
            'processo_seletivo' => 'portal_processo_seletivo',
            'secretaria' => 'portal_secretaria_academica',
            'financeiro' => 'portal_financeiro',
            'exaluno' => 'portal_exaluno',
            'portal_diretoria' => 'portal_diretoria'
        ];
        
        // Sempre usar credenciais de desenvolvimento local (funciona)
        $dbname = $dev_db_mapping[$portal_name] ?? $portal_name;
        $host = 'localhost';
        $user = 'root';
        $pass = '';
        
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            throw new Exception("Erro de conexão PDO para $portal_name: " . $e->getMessage());
        }
    }
}

// Função para conexão multi-banco (acessa qualquer banco)
if (!function_exists('connectDBMulti')) {
    function connectDBMulti() {
        global $environment;

        if ($environment === 'production') {
            // Em produção, conectar com usuário que tem permissões cross-database
            // Usar as credenciais do portal da diretoria que parecem ter mais privilégios
            $host = 'localhost';
            $user = 'usergprotoc';  // Usuário do portal da diretoria
            $pass = '8092QNSANSnjlaskn0u2Jjhoiq02';  // Senha do portal da diretoria
        } else {
            // Credenciais de desenvolvimento
            $host = 'localhost';
            $user = 'root';
            $pass = '';
        }

        $mysqli = new mysqli($host, $user, $pass);

        if ($mysqli->connect_error) {
            throw new Exception("Erro de conexão multi-banco: " . $mysqli->connect_error);
        }

        return $mysqli;
    }
}
?>