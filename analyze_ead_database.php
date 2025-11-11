<?php
require_once 'includes/config.php';

// Conectar ao banco do EAD
try {
    $conn_ead = new mysqli($host, $user, $password, $dbname_ead);
    if ($conn_ead->connect_error) {
        die("Erro de conexão: " . $conn_ead->connect_error);
    }

    echo "<h1>📊 Análise Completa - Base de Dados EAD</h1>";

    // Verificar estrutura da tabela
    echo "<h2>🏗️ Estrutura da tabela 'chamados'</h2>";
    $sql = "DESCRIBE chamados";
    $result = $conn_ead->query($sql);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Verificar todos os registros
    echo "<h2>📋 Todos os registros da tabela</h2>";
    $sql = "SELECT * FROM chamados";
    $result = $conn_ead->query($sql);
    
    if ($result->num_rows == 0) {
        echo "<p>❌ <strong>Nenhum registro encontrado na tabela chamados!</strong></p>";
    } else {
        echo "<p>✅ <strong>{$result->num_rows} registros encontrados</strong></p>";
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        // Cabeçalho
        $first_row = $result->fetch_assoc();
        echo "<tr>";
        foreach(array_keys($first_row) as $key) {
            echo "<th>$key</th>";
        }
        echo "</tr>";
        
        // Primeira linha
        echo "<tr>";
        foreach($first_row as $value) {
            $display_value = $value === '' ? '<em style="color: red;">[VAZIO]</em>' : htmlspecialchars($value);
            echo "<td>$display_value</td>";
        }
        echo "</tr>";
        
        // Resto das linhas
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach($row as $value) {
                $display_value = $value === '' ? '<em style="color: red;">[VAZIO]</em>' : htmlspecialchars($value);
                echo "<td>$display_value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    // Verificar valores únicos na coluna categoria
    echo "<h2>🏷️ Valores únicos na coluna 'categoria'</h2>";
    $sql = "SELECT categoria, COUNT(*) as total FROM chamados GROUP BY categoria ORDER BY total DESC";
    $result = $conn_ead->query($sql);
    
    if ($result->num_rows == 0) {
        echo "<p>❌ Nenhuma categoria encontrada</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Categoria</th><th>Quantidade</th></tr>";
        while($row = $result->fetch_assoc()) {
            $categoria = $row['categoria'] === '' ? '<em style="color: red;">[VAZIO]</em>' : htmlspecialchars($row['categoria']);
            echo "<tr><td>$categoria</td><td>{$row['total']}</td></tr>";
        }
        echo "</table>";
    }

    // Verificar valores únicos na coluna status
    echo "<h2>📊 Valores únicos na coluna 'status'</h2>";
    $sql = "SELECT status, COUNT(*) as total FROM chamados GROUP BY status ORDER BY total DESC";
    $result = $conn_ead->query($sql);
    
    if ($result->num_rows == 0) {
        echo "<p>❌ Nenhum status encontrado</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Status</th><th>Quantidade</th></tr>";
        while($row = $result->fetch_assoc()) {
            $status = $row['status'] === '' ? '<em style="color: red;">[VAZIO]</em>' : htmlspecialchars($row['status']);
            echo "<tr><td>$status</td><td>{$row['total']}</td></tr>";
        }
        echo "</table>";
    }

    echo "<h2>💡 Sugestões para melhorar visualização</h2>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #007cba;'>";
    echo "<p><strong>Para ter gráficos mais informativos:</strong></p>";
    echo "<ol>";
    echo "<li>Adicionar mais registros na tabela com diferentes categorias</li>";
    echo "<li>Preencher as categorias vazias com valores apropriados</li>";
    echo "<li>Utilizar ENUMs ou valores padrão para categorias</li>";
    echo "</ol>";
    
    echo "<p><strong>Exemplo de inserção:</strong></p>";
    echo "<code>";
    echo "INSERT INTO chamados (manifestacao, status, categoria) VALUES<br>";
    echo "('Problemas com login', 'aberto', 'Suporte Técnico'),<br>";
    echo "('Solicitar certificado', 'fechado', 'Certificados'),<br>";
    echo "('Dúvidas sobre curso', 'em_andamento', 'Atendimento'),<br>";
    echo "('Material não carrega', 'aberto', 'Suporte Técnico'),<br>";
    echo "('Segunda via diploma', 'fechado', 'Documentos');<br>";
    echo "</code>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
} finally {
    if (isset($conn_ead)) {
        $conn_ead->close();
    }
}
?>