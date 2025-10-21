<?php
session_start();

// Verificar se usuário já está logado
if (isset($_SESSION['usuario_logado'])) {
    header('Location: dashboard.php');
    exit;
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Autenticação simples (ajuste conforme necessário)
    if ($usuario === 'admin' && $senha === '123456') {
        $_SESSION['usuario_logado'] = true;
        $_SESSION['usuario_nome'] = 'Diretor';
        header('Location: dashboard.php');
        exit;
    } else {
        $erro = 'Usuário ou senha incorretos';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Diretoria Drummond</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="https://drummond.com.br/wp-content/uploads/2020/06/logo-drumond-azul.svg" alt="Logo Drummond" class="logo-img">
                <h1>Portal Diretoria</h1>
                <p>Consolidação de Dados dos Portais</p>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="usuario">Usuário:</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>

                <?php if (isset($erro)): ?>
                    <div class="erro"><?php echo $erro; ?></div>
                <?php endif; ?>

                <button type="submit" class="btn-login">Entrar</button>
            </form>

            <div class="footer">
                <p>Dashboard Executivo - Portais Drummond</p>
            </div>
        </div>
    </div>
</body>
</html>