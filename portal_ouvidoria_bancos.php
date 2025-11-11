<?php
// Portal Ouvidoria - Configuração de Bancos
// Este arquivo demonstra como configurar uma lista de bancos para o portal ouvidoria

// Array de bancos conforme solicitado
$bancos = [
    'bb' => [
        'nome' => 'Banco do Brasil',
        'codigo' => '001',
        'logo' => 'assets/img/bancos/bb.png',
        'cor_primaria' => '#1f4e79',
        'cor_secundaria' => '#f8b500'
    ],
    'itau' => [
        'nome' => 'Banco Itaú',
        'codigo' => '341',
        'logo' => 'assets/img/bancos/itau.png',
        'cor_primaria' => '#ec7000',
        'cor_secundaria' => '#003366'
    ],
    'bradesco' => [
        'nome' => 'Banco Bradesco',
        'codigo' => '237',
        'logo' => 'assets/img/bancos/bradesco.png',
        'cor_primaria' => '#c41e3a',
        'cor_secundaria' => '#ffffff'
    ],
    'santander' => [
        'nome' => 'Banco Santander',
        'codigo' => '033',
        'logo' => 'assets/img/bancos/santander.png',
        'cor_primaria' => '#ec0000',
        'cor_secundaria' => '#ffffff'
    ],
    'caixa' => [
        'nome' => 'Caixa Econômica Federal',
        'codigo' => '104',
        'logo' => 'assets/img/bancos/caixa.png',
        'cor_primaria' => '#0066b3',
        'cor_secundaria' => '#ffb300'
    ],
    'sicoob' => [
        'nome' => 'SICOOB',
        'codigo' => '756',
        'logo' => 'assets/img/bancos/sicoob.png',
        'cor_primaria' => '#00a651',
        'cor_secundaria' => '#ffffff'
    ],
    'nubank' => [
        'nome' => 'Nubank',
        'codigo' => '260',
        'logo' => 'assets/img/bancos/nubank.png',
        'cor_primaria' => '#8a05be',
        'cor_secundaria' => '#ffffff'
    ],
    'inter' => [
        'nome' => 'Banco Inter',
        'codigo' => '077',
        'logo' => 'assets/img/bancos/inter.png',
        'cor_primaria' => '#ff6900',
        'cor_secundaria' => '#ffffff'
    ]
];

// Função para obter lista de bancos
function getBancosList() {
    global $bancos;
    return $bancos;
}

// Função para obter dados de um banco específico
function getBanco($codigo_banco) {
    global $bancos;
    return isset($bancos[$codigo_banco]) ? $bancos[$codigo_banco] : null;
}

// Função para gerar options de select com bancos
function getBancosSelectOptions($selected = '') {
    global $bancos;
    $options = '<option value="">Selecione um banco</option>';
    
    foreach ($bancos as $codigo => $banco) {
        $selectedAttr = ($selected == $codigo) ? 'selected' : '';
        $options .= '<option value="' . $codigo . '" ' . $selectedAttr . '>';
        $options .= $banco['nome'] . ' (' . $banco['codigo'] . ')';
        $options .= '</option>';
    }
    
    return $options;
}

// Função para filtrar bancos por código ou nome
function filtrarBancos($termo) {
    global $bancos;
    $resultados = [];
    
    $termo = strtolower($termo);
    
    foreach ($bancos as $codigo => $banco) {
        if (strpos(strtolower($banco['nome']), $termo) !== false || 
            strpos($banco['codigo'], $termo) !== false ||
            strpos($codigo, $termo) !== false) {
            $resultados[$codigo] = $banco;
        }
    }
    
    return $resultados;
}

// Exemplo de uso
if (isset($_GET['demo']) && $_GET['demo'] == '1') {
    echo "<h2>Portal Ouvidoria - Configuração de Bancos</h2>";
    
    echo "<h3>Lista Completa de Bancos:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Código</th><th>Nome</th><th>Código Bancário</th><th>Cor Primária</th></tr>";
    
    foreach ($bancos as $codigo => $banco) {
        echo "<tr>";
        echo "<td>" . $codigo . "</td>";
        echo "<td>" . $banco['nome'] . "</td>";
        echo "<td>" . $banco['codigo'] . "</td>";
        echo "<td style='background-color: " . $banco['cor_primaria'] . "; color: white;'>" . $banco['cor_primaria'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Select de Bancos:</h3>";
    echo "<select style='width: 300px; padding: 5px;'>";
    echo getBancosSelectOptions();
    echo "</select>";
    
    echo "<h3>Busca por Termo (exemplo: 'itau'):</h3>";
    $busca = filtrarBancos('itau');
    foreach ($busca as $codigo => $banco) {
        echo "<p><strong>" . $banco['nome'] . "</strong> - Código: " . $banco['codigo'] . "</p>";
    }
    
    echo "<h3>Integração com Dashboard:</h3>";
    echo "<p>Para integrar com o dashboard, você pode usar essas funções nas suas consultas SQL:</p>";
    echo "<pre>";
    echo "// Exemplo de uso em consultas\n";
    echo "SELECT \n";
    echo "  banco_codigo,\n";
    echo "  COUNT(*) as total_chamados\n";
    echo "FROM " . TABLE_OUVIDORIA . "\n";
    echo "WHERE banco_codigo IN ('" . implode("','", array_keys($bancos)) . "')\n";
    echo "GROUP BY banco_codigo";
    echo "</pre>";
}
?>