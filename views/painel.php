<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?>!</h1>

    <p>Você está logado no sistema.</p>

    <a href="listar_usuarios.php">Listar Usuários</a><br><br>
    <a href="logout.php">Sair</a>
</body>
</html>