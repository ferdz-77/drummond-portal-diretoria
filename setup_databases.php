<?php
/**
 * Script de Setup dos Bancos de Desenvolvimento
 * Cria os bancos de dados necessários se não existirem
 */

// Configurações do banco local
$host = 'localhost';
$user = 'root';
$password = '';

$databases = [
    'portal_ouvidoria',
    'portal_ead',
    'portal_processo_seletivo',
    'portal_secretaria_academica',
    'portal_financeiro',
    'portal_exaluno'
];

echo "🔧 CRIANDO BANCOS DE DESENVOLVIMENTO\n";
echo str_repeat("=", 50) . "\n";

try {
    // Conecta ao MySQL sem especificar banco
    $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($databases as $dbName) {
        try {
            // Verifica se o banco já existe
            $stmt = $pdo->query("SHOW DATABASES LIKE '$dbName'");
            $exists = $stmt->fetch();

            if ($exists) {
                echo "✅ Banco '$dbName' já existe\n";
            } else {
                // Cria o banco
                $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                echo "🆕 Banco '$dbName' criado com sucesso\n";
            }
        } catch (Exception $e) {
            echo "❌ Erro ao verificar/criar banco '$dbName': " . $e->getMessage() . "\n";
        }
    }

    echo "\n🎉 Setup concluído! Todos os bancos estão prontos.\n";

} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    echo "💡 Verifique se o MySQL/XAMPP está rodando\n";
}
?></content>
<parameter name="filePath">c:\xampp\htdocs\portal_diretoria\setup_databases.php