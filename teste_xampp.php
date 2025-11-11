<?php
// Teste com credenciais padrão do XAMPP
echo "Testando conexão com credenciais padrão do XAMPP...<br>";

$conn = new mysqli('localhost', 'root', '', 'portal_diretoria');

if ($conn->connect_error) {
    echo "Erro: " . $conn->connect_error . "<br>";
} else {
    echo "✅ Conexão bem-sucedida com portal_diretoria!<br>";

    // Verificar se existem outros bancos
    $result = $conn->query("SHOW DATABASES");
    echo "Bancos disponíveis:<br><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['Database'] . "</li>";
    }
    echo "</ul>";

    $conn->close();
}
?>