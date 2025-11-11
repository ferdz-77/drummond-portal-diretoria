<?php
// Configuração simplificada para desenvolvimento local - Atualizado em 20/10/2025

// Carregar variáveis do arquivo .env se existir
if (file_exists(__DIR__ . '/../.env')) {
    $envFile = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        if (strpos($line, '#') === 0 || strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

$environment = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'development';

// Constantes para nomes de tabelas e colunas
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

// Função para conectar a qualquer portal
function connectDBEnvironment($portal_name) {
    $dev_db_mapping = [
        'ouvidoria' => 'portal_ouvidoria',
        'ead' => 'portal_ead',
        'processo_seletivo' => 'portal_processo_seletivo',
        'secretaria' => 'portal_secretaria_academica',
        'financeiro' => 'portal_financeiro',
        'exaluno' => 'portal_exaluno',
        'portal_diretoria' => 'portal_diretoria'
    ];

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

// Função para conexão multi-banco
function connectDBMulti() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';

    $mysqli = new mysqli($host, $user, $pass);

    if ($mysqli->connect_error) {
        throw new Exception("Erro de conexão multi-banco: " . $mysqli->connect_error);
    }

    return $mysqli;
}

// Função para conectar ao portal diretoria
function connectPortalDiretoria() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'portal_diretoria';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("Erro de conexão PDO com portal_diretoria: " . $e->getMessage());
    }
}

// Função para conectar a um banco específico (compatível com código antigo)
if (!function_exists('connectDB')) {
    function connectDB($dbname) {
        global $environment;
        
        if ($environment === 'production') {
            // Credenciais específicas por banco para produção
            $db_credentials = [
                'bdsolicita_atendimento' => [
                    'host' => 'localhost',
                    'user' => 'bdsolatend',
                    'pass' => 'opqwioihjOHUQHOQWNNA234',
                    'dbname' => 'bdsolicita_atendimento'
                ],
                'dbead' => [
                    'host' => 'localhost',
                    'user' => 'eaduseroot',
                    'pass' => 'OPHFQOIfOHOUHOou-039-',
                    'dbname' => 'dbead'
                ],
                'pseldb' => [
                    'host' => 'localhost',
                    'user' => 'pseluserdb',
                    'pass' => 'gpoaGGWOIJW5874ajds123478',
                    'dbname' => 'pseldb'
                ],
                'dbsecretacad' => [
                    'host' => 'localhost',
                    'user' => 'dbsecretacaduser',
                    'pass' => 'agkljUHP79654AJjosh27Y616',
                    'dbname' => 'dbsecretacad'
                ],
                'fini' => [
                    'host' => 'localhost',
                    'user' => 'userfinidb',
                    'pass' => 'AJHPFGiuhpuwh093876524yr',
                    'dbname' => 'fini'
                ],
                'dbgproto' => [
                    'host' => 'localhost',
                    'user' => 'usergprotoc',
                    'pass' => '8092QNSANSnjlaskn0u2Jjhoiq02',
                    'dbname' => 'dbgproto'
                ]
            ];

            // Encontrar as credenciais do banco específico
            $credenciais = null;
            foreach ($db_credentials as $db_name => $creds) {
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
                throw new Exception("Erro de conexão PDO para $dbname: " . $e->getMessage());
            }
        } else {
            // Desenvolvimento - usar credenciais padrão
            $dev_db_mapping = [
                'portal_ouvidoria' => 'portal_ouvidoria',
                'portal_ead' => 'portal_ead',
                'portal_processo_seletivo' => 'portal_processo_seletivo',
                'portal_secretaria_academica' => 'portal_secretaria_academica',
                'portal_financeiro' => 'portal_financeiro',
                'portal_exaluno' => 'portal_exaluno',
                'portal_diretoria' => 'portal_diretoria'
            ];

            $actual_dbname = $dev_db_mapping[$dbname] ?? $dbname;
            $host = 'localhost';
            $user = 'root';
            $pass = '';

            try {
                $pdo = new PDO("mysql:host=$host;dbname=$actual_dbname;charset=utf8", $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (PDOException $e) {
                throw new Exception("Erro de conexão PDO para $dbname: " . $e->getMessage());
            }
        }
    }
}

// Função helper para mapear nomes de bancos baseado no ambiente
function getProductionDatabaseName($development_name) {
    global $environment;

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

        // Mapeamento direto (sem prefixo) - usado nos arquivos data_*.php
        'ouvidoria' => 'bdsolicita_atendimento',
        'ead' => 'dbead',
        'processo_seletivo' => 'pseldb',
        'secretaria' => 'dbsecretacad',
        'financeiro' => 'fini',
        'exaluno' => 'bdsolicita_atendimento'
    ];

    if ($environment === 'production') {
        // Em produção, retornar o nome do banco de produção
        return $database_mapping[$development_name] ?? $development_name;
    } else {
        // Em desenvolvimento, converter para nome com prefixo portal_
        $dev_mapping = [
            'ouvidoria' => 'portal_ouvidoria',
            'ead' => 'portal_ead',
            'processo_seletivo' => 'portal_processo_seletivo',
            'secretaria' => 'portal_secretaria_academica',
            'financeiro' => 'portal_financeiro',
            'exaluno' => 'portal_exaluno'
        ];
        return $dev_mapping[$development_name] ?? $development_name;
    }
}
?>