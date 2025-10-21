<?php
// Exemplo de consultas para verificar notificações com controle de e-mail
// Conectar ao banco de dados
$conn = new mysqli("localhost", "root", "", "portal_diretoria");

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// 1. Verificar todas as notificações com status de e-mail
echo "<h3>📧 Status de Envio de E-mails das Notificações</h3>";
$sql = "SELECT id, mensagem, tipo, lida, email_enviado, data FROM notificacoes ORDER BY data DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Mensagem</th><th>Tipo</th><th>Lida</th><th>E-mail Enviado</th><th>Data</th></tr>";

    while($row = $result->fetch_assoc()) {
        $status_email = $row["email_enviado"] ? "✅ Sim" : "❌ Não";
        $status_lida = $row["lida"] ? "✅ Sim" : "❌ Não";

        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . htmlspecialchars($row["mensagem"]) . "</td>";
        echo "<td>" . $row["tipo"] . "</td>";
        echo "<td>" . $status_lida . "</td>";
        echo "<td>" . $status_email . "</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($row["data"])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhuma notificação encontrada.</p>";
}

// 2. Estatísticas de notificações críticas
echo "<h3>📊 Estatísticas de Notificações Críticas</h3>";
$sql_stats = "SELECT
    COUNT(*) as total_criticas,
    SUM(CASE WHEN email_enviado = TRUE THEN 1 ELSE 0 END) as emails_enviados,
    SUM(CASE WHEN email_enviado = FALSE THEN 1 ELSE 0 END) as emails_pendentes
    FROM notificacoes WHERE tipo = 'critical'";

$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();

echo "<ul>";
echo "<li><strong>Total de notificações críticas:</strong> " . $stats['total_criticas'] . "</li>";
echo "<li><strong>E-mails enviados:</strong> " . $stats['emails_enviados'] . "</li>";
echo "<li><strong>E-mails pendentes:</strong> " . $stats['emails_pendentes'] . "</li>";
echo "</ul>";

$conn->close();
?>