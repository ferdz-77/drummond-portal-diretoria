<?php
echo "🔍 SISTEMA DE NOTIFICAÇÕES - DASHBOARD DRUMMOND\n";
echo str_repeat("=", 50) . "\n\n";

echo "📊 STATUS ATUAL DOS SLAs:\n";
echo str_repeat("-", 30) . "\n";

// Carregar dados dos portais
require_once 'includes/config.php';
require_once 'data/data_ouvidoria.php';
require_once 'data/data_ead.php';
require_once 'data/data_processo_seletivo.php';
require_once 'data/data_secretaria.php';
require_once 'data/data_financeiro.php';
require_once 'data/data_exaluno.php';

$portais = [
    'Ouvidoria' => getSLAMedioOuvidoria(),
    'EAD' => getSLAMedioEAD(),
    'Processo Seletivo' => getSLAMedioProcessoSeletivo(),
    'Secretaria Acadêmica' => getTempoMedioSecretaria(),
    'Financeiro' => getSLAMedioFinanceiro(),
    'Ex-Aluno' => getSLAMedioExAluno()
];

$limite_critico = 10; // Dias - nível crítico
$limite_atencao = 5;  // Dias - nível de atenção

echo "Limites definidos:\n";
echo "- Crítico: > {$limite_critico} dias\n";
echo "- Atenção: > {$limite_atencao} dias\n\n";

echo "SLAs atuais por portal:\n";
foreach ($portais as $portal => $sla) {
    echo "- {$portal}: {$sla} dias\n";
}

echo "\n🚨 ANÁLISE DE NOTIFICAÇÕES:\n";
echo str_repeat("-", 30) . "\n";

$notificacoes_geradas = 0;

foreach ($portais as $portal => $sla) {
    if ($sla > $limite_critico) {
        $tipo = 'CRÍTICA';
        $emoji = '🚨';
        $mensagem = "{$emoji} SLA do Portal {$portal} CRÍTICO: {$sla} dias (limite: {$limite_critico} dias).";
        echo "❌ {$tipo}: {$mensagem}\n";
        $notificacoes_geradas++;
    } elseif ($sla > $limite_atencao) {
        $tipo = 'ATENÇÃO';
        $emoji = '⚠️';
        $mensagem = "{$emoji} SLA do Portal {$portal} em ATENÇÃO: {$sla} dias (limite: {$limite_critico} dias).";
        echo "⚠️ {$tipo}: {$mensagem}\n";
        $notificacoes_geradas++;
    } else {
        echo "✅ {$portal}: SLA OK ({$sla} dias)\n";
    }
}

echo "\n📧 SISTEMA DE NOTIFICAÇÕES:\n";
echo str_repeat("-", 30) . "\n";
echo "• Notificações geradas nesta análise: {$notificacoes_geradas}\n";
echo "• Como funciona:\n";
echo "  - Sistema verifica SLAs a cada carregamento do dashboard\n";
echo "  - Notificações são inseridas apenas uma vez por dia\n";
echo "  - E-mails são enviados apenas para notificações críticas\n";
echo "  - Usuário pode marcar notificações como lidas\n";

echo "\n💾 BANCO DE DADOS:\n";
echo str_repeat("-", 30) . "\n";

// Verificar notificações existentes
$conn = new mysqli("localhost", "root", "", "portal_diretoria");
$sql = "SELECT COUNT(*) as total, SUM(CASE WHEN lida = FALSE THEN 1 ELSE 0 END) as nao_lidas FROM notificacoes WHERE usuario_id = 'admin'";
$result = $conn->query($sql);
$stats = $result->fetch_assoc();

echo "• Total de notificações no banco: {$stats['total']}\n";
echo "• Não lidas: {$stats['nao_lidas']}\n";

$conn->close();

echo "\n🔄 PARA ATUALIZAR NOTIFICAÇÕES:\n";
echo str_repeat("-", 30) . "\n";
echo "• Acesse o dashboard via navegador\n";
echo "• O sistema verifica automaticamente os SLAs\n";
echo "• Novas notificações são geradas se necessário\n";
?>