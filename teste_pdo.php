<?php
// Teste de PDO
echo "Testando PDO...<br>";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=portal_ead;charset=utf8", "root", "");
    echo "PDO OK<br>";
    $pdo = null;
} catch (Exception $e) {
    echo "Erro PDO: " . $e->getMessage() . "<br>";
}
?>