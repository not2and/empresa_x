<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Acesso inválido');

$id = $_POST['id'] ?? null;

if (empty($id)) {
    echo json_encode(['status' => 'error', 'message' => 'ID não informado.']);
    exit;
}

try {
    // Verifica se há funcionários vinculados
    $stmt_check = $pdo->prepare("SELECT COUNT(*) as total FROM funcionarios WHERE id_departamento = ?");
    $stmt_check->execute([$id]);
    $count = $stmt_check->fetch()['total'];

    if ($count > 0) {
        echo json_encode(['status' => 'error', 'message' => "Não é possível excluir: existem $count funcionário(s) vinculado(s)."]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM departamentos WHERE id_departamento = ?");
    $stmt->execute([$id]);
    echo json_encode(['status' => 'success', 'message' => 'Departamento excluído com sucesso!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
}
?>