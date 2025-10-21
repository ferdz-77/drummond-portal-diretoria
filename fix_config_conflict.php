<?php
// Script para resolver conflitos de config em produção
// Execute este arquivo uma vez para renomear config.php antigo

echo "<h1>Resolução de Conflitos de Config</h1>";

// Renomear config.php antigo para evitar conflitos
$config_antigo = 'includes/config.php';
$config_backup = 'includes/config_backup_old.php';

if (file_exists($config_antigo)) {
    if (rename($config_antigo, $config_backup)) {
        echo "<p style='color: green;'>✓ Arquivo $config_antigo renomeado para $config_backup</p>";
        echo "<p>O conflito de redeclaração de função foi resolvido!</p>";
    } else {
        echo "<p style='color: red;'>✗ Erro ao renomear $config_antigo</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Arquivo $config_antigo não encontrado localmente</p>";
    echo "<p>Em produção, você precisa renomear manualmente:</p>";
    echo "<code>mv includes/config.php includes/config_backup_old.php</code>";
}

echo "<p><strong>Após resolver o conflito, teste novamente:</strong></p>";
echo "<ul>";
echo "<li><a href='health_check.php'>Health Check</a></li>";
echo "<li><a href='production_diagnostic.php'>Diagnóstico Completo</a></li>";
echo "<li><a href='dashboard.php'>Dashboard Principal</a></li>";
echo "</ul>";

echo "<h2>Arquivos de Config Ativos:</h2>";
$configs = [
    'includes/config_env.php' => 'Principal (ATIVO)',
    'includes/config.php' => 'Antigo (CAUSA CONFLITO)',
    'includes/config_backup_old.php' => 'Backup (SEGURO)'
];

foreach ($configs as $arquivo => $status) {
    $exists = file_exists($arquivo) ? '✓' : '✗';
    $color = file_exists($arquivo) ? 'green' : 'red';
    echo "<p style='color: $color;'>$exists $arquivo - $status</p>";
}
?>