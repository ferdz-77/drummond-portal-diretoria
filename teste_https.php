<?php
echo "<h1>Teste HTTPS</h1>";
echo "<p>Protocolo: " . (isset($_SERVER['HTTPS']) ? 'HTTPS' : 'HTTP') . "</p>";
echo "<p>Data: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Arquivo: " . __FILE__ . "</p>";
?>