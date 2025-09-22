<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Acesso inválido');

$descricao = trim($_POST['descricao'] ?? '');

if (empty($descricao)) {
    echo json_encode(['status' => 'error', 'message' => 'Descrição é obrigatória.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO departamentos (descricao) VALUES (?)");
    $stmt->execute([$descricao]);
    echo json_encode(['status' => 'success', 'message' => 'Departamento cadastrado com sucesso!']);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Duplicate entry
        echo json_encode(['status' => 'error', 'message' => 'Este departamento já existe.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar: ' . $e->getMessage()]);
    }
}
?>