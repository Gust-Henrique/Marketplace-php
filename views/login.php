<?php
require_once '../controller/UsuarioController.php';

session_start();

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new UsuarioController();

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuario = $controller->login($email, $senha);

    if ($usuario) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        header('Location: painel.php');
        exit;
    } else {
        $mensagem = "Email ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    <?php if ($mensagem != ""): ?>
        <p><strong><?php echo $mensagem; ?></strong></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <input type="submit" value="Entrar">
    </form>

    <br>
    <a href="cadastrar_usuario.php">Não tem cadastro? Cadastre-se aqui</a>
</body>
</html>