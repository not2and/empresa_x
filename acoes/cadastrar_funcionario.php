<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Acesso inválido');

$nome = trim($_POST['nome'] ?? '');
$afastado = $_POST['afastado'] ?? 'não';
$id_departamento = $_POST['id_departamento'] ?? null;

if (empty($nome) || empty($id_departamento)) {
    echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO funcionarios (nome, afastado, id_departamento) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $afastado, $id_departamento]);
    echo json_encode(['status' => 'success', 'message' => 'Funcionário cadastrado com sucesso!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar: ' . $e->getMessage()]);
}
?>