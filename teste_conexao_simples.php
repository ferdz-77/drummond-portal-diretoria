<?php
// Teste simples de conexão com banco ouvidoria
$host = 'localhost';
$user = 'bdsolatend';
$pass = 'opqwioihjOHUQHOQWNNA234';
$dbname = 'bdsolicita_atendimento';

echo "Testando conexão com banco ouvidoria...<br>";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo "Erro de conexão: " . $conn->connect_error . "<br>";
} else {
    echo "✅ Conexão bem-sucedida!<br>";
    $conn->close();
}
?>