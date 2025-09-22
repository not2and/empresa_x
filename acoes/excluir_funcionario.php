<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Acesso inválido');

$id = $_POST['id'] ?? null;

if (empty($id)) {
    echo json_encode(['status' => 'error', 'message' => 'ID não informado.']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM funcionarios WHERE id_funcionario = ?");
    $stmt->execute([$id]);
    echo json_encode(['status' => 'success', 'message' => 'Funcionário excluído com sucesso!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
}
?>