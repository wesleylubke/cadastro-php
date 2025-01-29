<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php'); 
    exit;
}

require_once 'db_conexao.php';

$id = $_GET['id'] ?? null;

if ($id === null) {
    echo "ID não fornecido."; 
    header('Location: home.php'); 
    exit;
}

$sql = "SELECT cod FROM pessoa WHERE cod = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo "Registro não encontrado.";
    header('Location: home.php'); 
    exit;
}

$stmt->close();

$sql = "DELETE FROM pessoa_interesse WHERE fk_pessoa_cod = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$sql = "DELETE FROM pessoa WHERE cod = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Registro excluído com sucesso!";
    header('Location: home.php');
    exit;
} else {
    echo "Erro ao excluir o registro: " . $stmt->error;
}

$stmt->close();
$conexao->close();
?>
