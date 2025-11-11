<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Verificar se o ID da notificação foi fornecido
if (!isset($_POST['notification_id']) || !is_numeric($_POST['notification_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID da notificação inválido']);
    exit;
}

$notification_id = (int) $_POST['notification_id'];
$usuario_id = $_SESSION['usuario_id'] ?? 'admin';

// Conectar ao banco de dados
$conn = new mysqli("localhost", "root", "", "portal_diretoria");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco']);
    exit;
}

// Verificar se a notificação pertence ao usuário
$sql_check = "SELECT id FROM notificacoes WHERE id = ? AND usuario_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("is", $notification_id, $usuario_id);
$stmt_check->execute();

if ($stmt_check->get_result()->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Notificação não encontrada ou não pertence ao usuário']);
    exit;
}

// Marcar notificação como lida
$sql_update = "UPDATE notificacoes SET lida = TRUE WHERE id = ? AND usuario_id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("is", $notification_id, $usuario_id);

if ($stmt_update->execute()) {
    // Contar notificações não lidas restantes
    $sql_count = "SELECT COUNT(*) as count FROM notificacoes WHERE usuario_id = ? AND lida = FALSE";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("s", $usuario_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $count_data = $result_count->fetch_assoc();

    echo json_encode([
        'success' => true,
        'message' => 'Notificação marcada como lida',
        'unread_count' => $count_data['count']
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao marcar notificação como lida']);
}

$conn->close();
?>