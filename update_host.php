<?php
// Script para atualizar o host no config_env.php
if (!isset($_GET['host'])) {
    echo "<h1>❌ Erro: Host não especificado</h1>";
    echo "<p>Use: <code>update_host.php?host=NOME_DO_HOST</code></p>";
    exit;
}

$new_host = $_GET['host'];
$config_file = 'includes/config_env.php';

echo "<h1>🔄 Atualizando Host do Portal Diretoria</h1>";
echo "<p><strong>Novo host:</strong> <code>$new_host</code></p>";

// Ler o arquivo atual
$content = file_get_contents($config_file);

if ($content === false) {
    echo "<p style='color: red;'>❌ Erro ao ler o arquivo de configuração!</p>";
    exit;
}

// Procurar e substituir o host do portal_diretoria
$pattern = "/('portal_diretoria' => \[[\s\S]*?'host' => ')[^']*(')/";
$replacement = "$1$new_host$2";

$new_content = preg_replace($pattern, $replacement, $content);

if ($new_content === $content) {
    echo "<p style='color: red;'>❌ Não foi possível encontrar ou atualizar o host no arquivo!</p>";
    exit;
}

// Fazer backup do arquivo original
$backup_file = $config_file . '.backup.' . date('Y-m-d_H-i-s');
if (copy($config_file, $backup_file)) {
    echo "<p>✅ Backup criado: <code>$backup_file</code></p>";
} else {
    echo "<p style='color: orange;'>⚠️ Aviso: Não foi possível criar backup!</p>";
}

// Salvar o novo conteúdo
if (file_put_contents($config_file, $new_content)) {
    echo "<p style='color: green; font-weight: bold;'>✅ Host atualizado com sucesso!</p>";
    echo "<p>Arquivo modificado: <code>$config_file</code></p>";

    // Testar a nova configuração
    echo "<h2>🧪 Testando Nova Configuração</h2>";
    try {
        require_once $config_file;

        $conn = connectPortalDiretoria();
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM notificacoes");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        echo "<p style='color: green;'>✅ Conexão funcionando! {$result['total']} notificações encontradas.</p>";

        $conn->close();

    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Ainda há problemas: " . $e->getMessage() . "</p>";
    }

} else {
    echo "<p style='color: red;'>❌ Erro ao salvar as alterações!</p>";
}

echo "<br><p><a href='test_hosts.php'>← Voltar ao teste de hosts</a></p>";
echo "<p><a href='test_connections_detailed.php'>📊 Testar todas as conexões</a></p>";
?>