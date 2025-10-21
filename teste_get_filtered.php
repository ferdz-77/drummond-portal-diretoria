<?php
// Teste do get_filtered_data.php
session_start();
$_SESSION['usuario_logado'] = true;

echo "Testando get_filtered_data.php...<br>";

// Simular uma requisição POST
$_POST['portal'] = 'todos';
$_POST['periodo'] = 'todos';

try {
    require_once 'get_filtered_data.php';
    echo "Arquivo incluído com sucesso<br>";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "<br>";
}
?>