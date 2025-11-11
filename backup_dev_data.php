<?php
/**
 * Script de Backup dos Dados de Desenvolvimento
 * Cria backup dos dados atuais antes da migração
 */

// Configurações
$host = 'localhost';
$user = 'root';
$password = '';
$backupDir = __DIR__ . '/backups/';

// Cria diretório de backup se não existir
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$databases = [
    'portal_ouvidoria',
    'portal_ead',
    'portal_processo_seletivo',
    'portal_secretaria_academica',
    'portal_financeiro',
    'portal_exaluno'
];

$timestamp = date('Y-m-d_H-i-s');
$backupFile = $backupDir . "backup_dev_$timestamp.sql";

echo "💾 CRIANDO BACKUP DOS DADOS DE DESENVOLVIMENTO\n";
echo str_repeat("=", 60) . "\n";
echo "📁 Arquivo: $backupFile\n\n";

try {
    // Conecta ao MySQL
    $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlContent = "";
    $totalTables = 0;
    $totalRecords = 0;

    foreach ($databases as $dbName) {
        echo "📊 Processando: $dbName\n";

        try {
            // Conecta ao banco específico
            $dbPdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $user, $password);
            $dbPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Lista tabelas
            $stmt = $dbPdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                $totalTables++;

                // Estrutura da tabela
                $stmt = $dbPdo->query("SHOW CREATE TABLE `$table`");
                $createTable = $stmt->fetch(PDO::FETCH_ASSOC)['Create Table'];

                $sqlContent .= "\n-- Tabela: $table (Banco: $dbName)\n";
                $sqlContent .= "USE `$dbName`;\n";
                $sqlContent .= "$createTable;\n\n";

                // Dados da tabela
                $stmt = $dbPdo->query("SELECT * FROM `$table`");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($data) > 0) {
                    $columns = array_keys($data[0]);
                    $sqlContent .= "INSERT INTO `$table` (`" . implode('`,`', $columns) . "`) VALUES\n";

                    $values = [];
                    foreach ($data as $row) {
                        $rowValues = [];
                        foreach ($row as $value) {
                            if ($value === null) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = $dbPdo->quote($value);
                            }
                        }
                        $values[] = "(" . implode(',', $rowValues) . ")";
                    }

                    $sqlContent .= implode(",\n", $values) . ";\n\n";
                    $totalRecords += count($data);
                }
            }

        } catch (Exception $e) {
            echo "⚠️  Banco '$dbName' não encontrado ou vazio: " . $e->getMessage() . "\n";
        }
    }

    // Salva o arquivo de backup
    file_put_contents($backupFile, $sqlContent);

    echo "\n📊 RESUMO DO BACKUP:\n";
    echo "   • Tabelas: $totalTables\n";
    echo "   • Registros: $totalRecords\n";
    echo "   • Arquivo: " . basename($backupFile) . "\n\n";

    echo "✅ Backup concluído com sucesso!\n";
    echo "💡 Para restaurar: mysql -u root < $backupFile\n";

} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    echo "💡 Verifique se o MySQL/XAMPP está rodando\n";
}
?></content>
<parameter name="filePath">c:\xampp\htdocs\portal_diretoria\backup_dev_data.php