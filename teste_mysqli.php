<?php
// Teste simples de mysqli
echo "Testando mysqli...<br>";

if (class_exists('mysqli')) {
    echo "Classe mysqli existe<br>";
    
    $conn = new mysqli('localhost', 'root', '', 'portal_diretoria');
    if ($conn->connect_error) {
        echo "Erro: " . $conn->connect_error . "<br>";
    } else {
        echo "Conexão OK<br>";
        $conn->close();
    }
} else {
    echo "Classe mysqli NÃO existe<br>";
}
?>