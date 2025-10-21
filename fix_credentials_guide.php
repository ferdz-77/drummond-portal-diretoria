<?php
// Instruções para correção das credenciais
echo "<h1>📋 Guia de Correção das Credenciais de Produção</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;max-width:800px;} .step{background:#f8f9fa;padding:15px;margin:10px 0;border-left:4px solid #007bff;border-radius:5px;} .code{background:#f1f1f1;padding:10px;border-radius:3px;font-family:monospace;} .warning{background:#fff3cd;color:#856404;padding:10px;border-radius:5px;margin:10px 0;} .success{background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin:10px 0;}</style>";

echo "<div class='warning'>";
echo "<strong>⚠️ IMPORTANTE:</strong> Todas as conexões estão falhando com 'Access denied'. Isso significa que as credenciais estão incorretas ou os usuários não existem no servidor de produção.";
echo "</div>";

echo "<h2>🔍 Passo 1: Diagnosticar o Problema</h2>";
echo "<div class='step'>";
echo "<h3>Teste as conexões individualmente:</h3>";
echo "<p>Acesse: <a href='test_single_connection.php' target='_blank'>test_single_connection.php</a></p>";
echo "<p>Clique em cada portal para testar a conexão específica e ver o diagnóstico detalhado.</p>";
echo "</div>";

echo "<h2>🛠️ Passo 2: Verificar Credenciais no Servidor</h2>";
echo "<div class='step'>";
echo "<h3>Acesse o servidor de produção via SSH:</h3>";
echo "<div class='code'>ssh usuario@servidor-producao</div>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>Verifique se o MySQL está rodando:</h3>";
echo "<div class='code'>sudo systemctl status mysql</div>";
echo "<p>Ou para sistemas mais antigos:</p>";
echo "<div class='code'>sudo service mysql status</div>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>Conecte ao MySQL como root:</h3>";
echo "<div class='code'>mysql -u root -p</div>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>Liste os usuários relacionados ao Drummond:</h3>";
echo "<div class='code'>SELECT User, Host FROM mysql.user WHERE User LIKE '%drummond%' OR User LIKE '%proto%' OR User LIKE '%sol%' OR User LIKE '%ead%' OR User LIKE '%psel%' OR User LIKE '%secret%' OR User LIKE '%fini%';</div>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>Verifique os bancos de dados existentes:</h3>";
echo "<div class='code'>SHOW DATABASES;</div>";
echo "</div>";

echo "<h2>🔧 Passo 3: Corrigir Credenciais</h2>";
echo "<div class='step'>";
echo "<h3>Para cada usuário que não existe, crie-o:</h3>";
echo "<div class='code'>CREATE USER 'nome_usuario'@'localhost' IDENTIFIED BY 'senha';</div>";
echo "<div class='code'>GRANT ALL PRIVILEGES ON nome_banco.* TO 'nome_usuario'@'localhost';</div>";
echo "<div class='code'>FLUSH PRIVILEGES;</div>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>Ou, se os usuários existem mas as senhas estão erradas, altere:</h3>";
echo "<div class='code'>ALTER USER 'nome_usuario'@'localhost' IDENTIFIED BY 'nova_senha';</div>";
echo "</div>";

echo "<h2>📝 Passo 4: Atualizar config_env.php</h2>";
echo "<div class='step'>";
echo "<p>Após corrigir as credenciais no banco, atualize o arquivo <code>includes/config_env.php</code> com as informações corretas.</p>";
echo "<p><strong>ATENÇÃO:</strong> As credenciais estão hardcoded no arquivo para segurança.</p>";
echo "</div>";

echo "<h2>✅ Passo 5: Testar Novamente</h2>";
echo "<div class='step'>";
echo "<p>Após as correções, teste novamente:</p>";
echo "<p>• <a href='test_connections_detailed.php' target='_blank'>Teste completo</a></p>";
echo "<p>• <a href='test_single_connection.php' target='_blank'>Teste individual</a></p>";
echo "<p>• <a href='dashboard_real.php' target='_blank'>Dashboard com dados reais</a></p>";
echo "</div>";

echo "<h2>🚨 Plano B: Usar Dashboard com Fallback</h2>";
echo "<div class='step'>";
echo "<p>Enquanto as credenciais não são corrigidas, use:</p>";
echo "<p><a href='dashboard_mock_fallback.php' target='_blank'>dashboard_mock_fallback.php</a></p>";
echo "<p>Este dashboard mostra dados reais quando possível e dados simulados quando as conexões falham.</p>";
echo "</div>";

echo "<h2>📞 Contato de Emergência</h2>";
echo "<div class='warning'>";
echo "<strong>Se você não tem acesso ao servidor MySQL ou não sabe como corrigir as credenciais:</strong><br>";
echo "• Entre em contato com o administrador de banco de dados da Drummond<br>";
echo "• Forneça os resultados dos testes acima<br>";
echo "• Peça para verificar se os usuários e bancos existem";
echo "</div>";

echo "<h2>🔍 Credenciais Atuais (para referência)</h2>";
echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
echo "<tr><th>Portal</th><th>Usuário</th><th>Banco</th><th>Status</th></tr>";

require_once 'includes/config_env.php';
foreach ($db_credentials as $portal => $creds) {
    $status = (isset($test_results[$portal]) && $test_results[$portal]['status'] === 'success') ? '✅ OK' : '❌ Falha';
    echo "<tr><td>$portal</td><td>{$creds['user']}</td><td>{$creds['dbname']}</td><td>$status</td></tr>";
}

echo "</table>";
?>