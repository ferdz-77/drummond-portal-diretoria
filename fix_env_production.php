<?php
// Script para corrigir o .env no servidor de produção
echo "🔧 CORREÇÃO DO .ENV PARA PRODUÇÃO\n";
echo "===================================\n\n";

$envPath = __DIR__ . '/.env';

if (!file_exists($envPath)) {
    echo "❌ Arquivo .env não encontrado!\n";
    exit(1);
}

echo "📂 Arquivo .env encontrado: $envPath\n\n";

echo "📄 Conteúdo atual:\n";
$content = file_get_contents($envPath);
echo "```\n$content\n```\n\n";

// Verificar se já está correto
if (strpos($content, 'APP_ENV=production') !== false) {
    echo "✅ Arquivo .env já está configurado para produção!\n";
    exit(0);
}

// Fazer backup
$backupPath = $envPath . '.backup.' . date('Y-m-d_H-i-s');
if (copy($envPath, $backupPath)) {
    echo "✅ Backup criado: $backupPath\n\n";
} else {
    echo "❌ Falha ao criar backup!\n";
    exit(1);
}

// Substituir APP_ENV=development por APP_ENV=production
$newContent = str_replace('APP_ENV=development', 'APP_ENV=production', $content);

if ($newContent === $content) {
    echo "❌ Não foi possível encontrar 'APP_ENV=development' para substituir\n";
    exit(1);
}

echo "🔄 Aplicando correção...\n";
if (file_put_contents($envPath, $newContent)) {
    echo "✅ Arquivo .env corrigido com sucesso!\n\n";

    echo "📄 Novo conteúdo:\n";
    echo "```\n$newContent\n```\n\n";

    echo "🔍 Verificando correção...\n";
    $verifyContent = file_get_contents($envPath);
    if (strpos($verifyContent, 'APP_ENV=production') !== false) {
        echo "✅ Verificação: APP_ENV=production encontrado\n";
    } else {
        echo "❌ Verificação falhou: APP_ENV=production não encontrado\n";
    }

    echo "\n🎯 PRÓXIMOS PASSOS:\n";
    echo "1. Execute novamente: https://gestao-protocolos.drummond.com.br/debug_production.php\n";
    echo "2. Verifique se o ambiente agora é detectado como 'production'\n";
    echo "3. Teste o dashboard: https://gestao-protocolos.drummond.com.br/dashboard.php\n";

} else {
    echo "❌ Falha ao salvar o arquivo .env!\n";
    exit(1);
}
?>