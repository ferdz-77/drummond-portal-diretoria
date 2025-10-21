<?php
$files = ['data_ead.php', 'data_processo_seletivo.php', 'data_secretaria.php', 'data_financeiro.php', 'data_exaluno.php'];

foreach ($files as $file) {
    $filepath = "data/$file";
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        $updated = str_replace('getConnection(', 'connectDB(', $content);
        file_put_contents($filepath, $updated);
        echo "✅ Corrigido: $file\n";
    } else {
        echo "❌ Não encontrado: $file\n";
    }
}

echo "Processo concluído!\n";
?>