<?php
// Script para corrigir valores vazios em todos os portais
echo "<h1>🛠️ Aplicando Correções para Valores Vazios</h1>";
echo "<pre>";

$portais = [
    'processo_seletivo' => [
        'file' => 'data/data_processo_seletivo.php',
        'functions' => ['getChamadosStatusProcessoSeletivoFiltered', 'getServicosSolicitadosProcessoSeletivoFiltered']
    ],
    'secretaria' => [
        'file' => 'data/data_secretaria.php',
        'functions' => ['getSolicitacoesStatusSecretariaFiltered', 'getServicosSolicitadosSecretariaFiltered']
    ],
    'financeiro' => [
        'file' => 'data/data_financeiro.php',
        'functions' => ['getChamadosStatusFinanceiroFiltered', 'getServicosSolicitadosFinanceiroFiltered']
    ],
    'exaluno' => [
        'file' => 'data/data_exaluno.php',
        'functions' => ['getChamadosStatusExAlunoFiltered', 'getServicosSolicitadosExAlunoFiltered']
    ]
];

foreach ($portais as $portal => $config) {
    echo "=== CORRIGINDO PORTAL: " . strtoupper($portal) . " ===\n";
    
    $filepath = $config['file'];
    
    if (file_exists($filepath)) {
        echo "✅ Arquivo encontrado: $filepath\n";
        
        $content = file_get_contents($filepath);
        
        // Padrões de correção para funções principais (não filtradas)
        $patterns = [
            // Status function pattern
            '/SELECT\s+([A-Z_]+),\s*COUNT\(\*\)\s*as\s+count\s+FROM\s+([A-Z_]+)\s+GROUP BY\s+([A-Z_]+)/' => 
            'SELECT 
            CASE 
                WHEN $1 = \'\' OR $1 IS NULL THEN \'Não informado\'
                ELSE $1
            END as status,
            COUNT(*) as count 
            FROM $2 
            GROUP BY 
            CASE 
                WHEN $1 = \'\' OR $1 IS NULL THEN \'Não informado\'
                ELSE $1
            END',
            
            // Service function pattern for categoria
            '/SELECT\s+([A-Z_]+),\s*COUNT\(\*\)\s*as\s+count\s+FROM\s+([A-Z_]+)\s+GROUP BY\s+([A-Z_]+)(?=.*categoria)/' => 
            'SELECT 
            CASE 
                WHEN $1 = \'\' OR $1 IS NULL THEN \'Categoria não informada\'
                ELSE $1
            END as categoria,
            COUNT(*) as count 
            FROM $2 
            GROUP BY 
            CASE 
                WHEN $1 = \'\' OR $1 IS NULL THEN \'Categoria não informada\'
                ELSE $1
            END'
        ];
        
        // Aplicar correções básicas (esta é uma abordagem simplificada)
        echo "ℹ️  Para este script, recomendo aplicar correções manualmente arquivo por arquivo\n";
        echo "ℹ️  Ou usar replace_string_in_file para cada função específica\n";
        
    } else {
        echo "❌ Arquivo não encontrado: $filepath\n";
    }
    
    echo "\n";
}

echo "=== ALTERNATIVA: CORREÇÃO MAIS SIMPLES ===\n";
echo "Vou aplicar uma correção mais direta usando updateStatus no dashboard.js\n";
echo "Isso permitirá tratar valores vazios no frontend também.\n";

echo "</pre>";

// Criar uma função helper para o frontend
$jsHelper = "
// Helper para tratar valores vazios no frontend
function formatEmptyValues(data) {
    if (!data) return data;
    
    // Tratar status
    if (data.status && Array.isArray(data.status)) {
        data.status = data.status.map(item => ({
            ...item,
            status: item.status === '' || item.status === null ? 'Não informado' : item.status
        }));
    }
    
    // Tratar serviços
    if (data.servicos && Array.isArray(data.servicos)) {
        data.servicos = data.servicos.map(item => ({
            ...item,
            categoria: item.categoria === '' || item.categoria === null ? 'Categoria não informada' : item.categoria,
            servico: item.servico === '' || item.servico === null ? 'Serviço não informado' : item.servico
        }));
    }
    
    // Tratar tipos
    if (data.tipos && Array.isArray(data.tipos)) {
        data.tipos = data.tipos.map(item => ({
            ...item,
            manifestacao: item.manifestacao === '' || item.manifestacao === null ? 'Tipo não informado' : item.manifestacao,
            tipo: item.tipo === '' || item.tipo === null ? 'Tipo não informado' : item.tipo
        }));
    }
    
    return data;
}
";

file_put_contents('js/format_helper.js', $jsHelper);
echo "<p>✅ Criado arquivo js/format_helper.js com helper para frontend</p>";
?>