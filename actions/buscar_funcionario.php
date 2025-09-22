<?php
require_once '../conexao.php';

$id = $_GET['id'] ?? null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID não fornecido']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT f.id_funcionario, f.nome, f.afastado, f.id_departamento
    FROM funcionarios f
    WHERE f.id_funcionario = ?
");
$stmt->execute([$id]);
$funcionario = $stmt->fetch();

if ($funcionario) {
    echo json_encode($funcionario);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Funcionário não encontrado']);
}
?>