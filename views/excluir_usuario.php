<?php
require_once '../controller/UsuarioController.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $controller = new UsuarioController();
    $mensagem = $controller->excluir($id);

    header('Location: listar_usuarios.php');
    exit;
} else {
    echo "ID do usuário não informado.";
    exit;
}
?>
