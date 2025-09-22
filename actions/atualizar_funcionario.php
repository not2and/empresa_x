<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Acesso inválido');

$id = $_POST['id_funcionario'] ?? null;
$nome = trim($_POST['nome'] ?? '');
$afastado = $_POST['afastado'] ?? 'não';
$id_departamento = $_POST['id_departamento'] ?? null;

if (empty($id) || empty($nome) || empty($id_departamento)) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE funcionarios SET nome = ?, afastado = ?, id_departamento = ? WHERE id_funcionario = ?");
    $stmt->execute([$nome, $afastado, $id_departamento, $id]);
    echo json_encode(['status' => 'success', 'message' => 'Funcionário atualizado com sucesso!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar: ' . $e->getMessage()]);
}
?>