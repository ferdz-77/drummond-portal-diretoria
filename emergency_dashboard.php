<?php
// MODO EMERGÊNCIA - CARREGAR APENAS BANCOS FUNCIONANDO
// Este arquivo força o carregamento apenas dos bancos que funcionam

echo "🚨 MODO EMERGÊNCIA - DASHBOARD SIMPLIFICADO\n";
echo "=============================================\n\n";

// Forçar produção mas com configuração mínima
$_ENV['APP_ENV'] = 'production';

// Configuração mínima apenas com bancos que funcionam
$working_databases = [
    'portal_diretoria' => [
        'host' => 'localhost',
        'user' => 'usergprotoc',
        'pass' => '8092QNSANSnjlaskn0u2Jjhoiq02',
        'dbname' => 'dbgproto'
    ]
];

// Carregar apenas configurações básicas
$environment = 'production';
$host = 'localhost';
$user = 'usergprotoc';
$password = '8092QNSANSnjlaskn0u2Jjhoiq02';
$dbname_portal_diretoria = 'dbgproto';

// Constantes mínimas
define('TABLE_OUVIDORIA', 'chamados');
define('COL_ID_OUVIDORIA', 'id');
define('COL_STATUS_OUVIDORIA', 'status');
define('COL_DATA_ABERTURA_OUVIDORIA', 'data_abertura');
define('COL_DATA_FECHAMENTO_OUVIDORIA', 'data_encerramento');
define('COL_TIPO_OUVIDORIA', 'manifestacao');
define('COL_DESCRICAO_OUVIDORIA', 'descricao');

// Função simplificada de conexão
function connectDB($dbname) {
    global $working_databases;

    if (isset($working_databases[$dbname])) {
        $creds = $working_databases[$dbname];
        try {
            $pdo = new PDO("mysql:host={$creds['host']};dbname={$creds['dbname']};charset=utf8",
                          $creds['user'], $creds['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (Exception $e) {
            throw new Exception("Banco $dbname não disponível no modo emergência");
        }
    } else {
        // Para bancos não funcionais, retornar PDO que falha graciosamente
        throw new Exception("Banco $dbname não disponível no modo emergência");
    }
}

function connectPortalDiretoria() {
    global $working_databases;

    $creds = $working_databases['portal_diretoria'];
    $conn = new mysqli($creds['host'], $creds['user'], $creds['pass'], $creds['dbname']);
    if ($conn->connect_error) {
        throw new Exception("Erro de conexão no modo emergência: " . $conn->connect_error);
    }
    return $conn;
}

// Simular dados vazios para bancos não funcionais
function getChamadosStatusOuvidoria() { return []; }
function getSLAMedioOuvidoria() { return 0; }
function getSLAMedioOuvidoriaMesAnterior() { return 0; }
function getTiposManifestacaoOuvidoria() { return []; }

function getChamadosStatusEAD() { return []; }
function getSLAMedioEAD() { return 0; }
function getSLAMedioEADMesAnterior() { return 0; }
function getServicosSolicitadosEAD() { return []; }

function getChamadosStatusProcessoSeletivo() { return []; }
function getSLAMedioProcessoSeletivo() { return 0; }
function getSLAMedioProcessoSeletivoMesAnterior() { return 0; }
function getServicosSolicitadosProcessoSeletivo() { return []; }

function getSolicitacoesStatusSecretaria() { return []; }
function getTempoMedioSecretaria() { return 0; }
function getTempoMedioSecretariaMesAnterior() { return 0; }
function getServicosSolicitadosSecretaria() { return []; }

function getChamadosStatusFinanceiro() { return []; }
function getSLAMedioFinanceiro() { return 0; }
function getSLAMedioFinanceiroMesAnterior() { return 0; }
function getServicosSolicitadosFinanceiro() { return []; }

function getChamadosStatusExAluno() { return []; }
function getSLAMedioExAluno() { return 0; }
function getSLAMedioExAlunoMesAnterior() { return 0; }
function getServicosSolicitadosExAluno() { return []; }

echo "✅ Modo emergência carregado\n";
echo "⚠️  Apenas notificações funcionarão\n";
echo "📊 Outros portais mostrarão dados vazios\n\n";

echo "<a href='dashboard.php?emergency=1'>🚨 ACESSAR DASHBOARD EM MODO EMERGÊNCIA</a>\n\n";

echo "<hr>\n";
echo "<h3>🔧 Ferramentas de Diagnóstico:</h3>\n";
echo "<a href='debug_production.php'>Debug de Produção</a><br>\n";
echo "<a href='diagnose_production.php'>Diagnóstico de Hosts</a><br>\n";
echo "<a href='test_all_databases.php'>Teste Completo de Bancos</a><br>\n";
?>