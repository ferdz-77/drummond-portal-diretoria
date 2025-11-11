<?php
require_once 'includes/config.php';

// Conectar ao banco do Financeiro
try {
    $conn_financeiro = new mysqli($host, $user, $password, $dbname_financeiro);
    if ($conn_financeiro->connect_error) {
        die("Erro de conexão: " . $conn_financeiro->connect_error);
    }

    echo "<h1>📊 Análise Portal Financeiro</h1>";

    // Verificar estrutura da tabela
    echo "<h2>🏗️ Estrutura da tabela 'chamados'</h2>";
    $sql = "DESCRIBE chamados";
    $result = $conn_financeiro->query($sql);
    
    if ($result) {
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
    }

    // Verificar todos os registros
    echo "<h2>📋 Todos os registros da tabela</h2>";
    $sql = "SELECT * FROM chamados LIMIT 10";
    $result = $conn_financeiro->query($sql);
    
    if ($result->num_rows == 0) {
        echo "<p>❌ <strong>Nenhum registro encontrado na tabela chamados!</strong></p>";
    } else {
        echo "<p>✅ <strong>{$result->num_rows} registros encontrados (mostrando primeiros 10)</strong></p>";
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        // Cabeçalho
        $first_row = $result->fetch_assoc();
        if ($first_row) {
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
        }
        echo "</table>";
    }

    // Verificar valores únicos na coluna categoria/tipo/servico
    echo "<h2>🏷️ Análise de Categorias/Tipos/Serviços</h2>";
    
    // Primeiro verificar quais colunas existem para categorização
    $result = $conn_financeiro->query("SHOW COLUMNS FROM chamados");
    $available_columns = [];
    
    while($row = $result->fetch_assoc()) {
        $col_name = $row['Field'];
        if (strpos($col_name, 'categoria') !== false || 
            strpos($col_name, 'tipo') !== false || 
            strpos($col_name, 'servico') !== false) {
            $available_columns[] = $col_name;
        }
    }
    
    echo "<h3>📋 Colunas disponíveis para categorização:</h3>";
    if (empty($available_columns)) {
        echo "<p>❌ Nenhuma coluna de categorização encontrada</p>";
    } else {
        echo "<ul>";
        foreach ($available_columns as $col) {
            echo "<li>$col</li>";
        }
        echo "</ul>";
        
        // Analisar cada coluna
        foreach ($available_columns as $col) {
            echo "<h4>📊 Análise da coluna '$col'</h4>";
            $sql = "SELECT $col, COUNT(*) as total FROM chamados GROUP BY $col ORDER BY total DESC";
            $result = $conn_financeiro->query($sql);
            
            if ($result && $result->num_rows > 0) {
                echo "<table border='1' style='border-collapse: collapse;'>";
                echo "<tr><th>$col</th><th>Quantidade</th></tr>";
                while($row = $result->fetch_assoc()) {
                    $value = $row[$col] === '' || $row[$col] === null ? '<em style="color: red;">[VAZIO/NULL]</em>' : htmlspecialchars($row[$col]);
                    echo "<tr><td>$value</td><td>{$row['total']}</td></tr>";
                }
                echo "</table>";
            }
        }
    }

    // Verificar valores únicos na coluna status
    echo "<h2>📊 Análise de Status</h2>";
    $sql = "SELECT status, COUNT(*) as total FROM chamados GROUP BY status ORDER BY total DESC";
    $result = $conn_financeiro->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Status</th><th>Quantidade</th></tr>";
        while($row = $result->fetch_assoc()) {
            $status = $row['status'] === '' || $row['status'] === null ? '<em style="color: red;">[VAZIO/NULL]</em>' : htmlspecialchars($row['status']);
            echo "<tr><td>$status</td><td>{$row['total']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Erro ao consultar status ou nenhum dado encontrado</p>";
    }

    // Testar função atual do sistema
    echo "<h2>🧪 Teste das Funções Atuais</h2>";
    
    require_once 'data/data_financeiro.php';
    
    echo "<h3>📊 Dados Status (função atual)</h3>";
    $status_data = getChamadosStatusFinanceiro();
    echo "<pre>";
    print_r($status_data);
    echo "</pre>";
    
    echo "<h3>📊 Dados Serviços (função atual)</h3>";
    $servicos_data = getServicosSolicitadosFinanceiro();
    echo "<pre>";
    print_r($servicos_data);
    echo "</pre>";

} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
} finally {
    if (isset($conn_financeiro)) {
        $conn_financeiro->close();
    }
}
?>