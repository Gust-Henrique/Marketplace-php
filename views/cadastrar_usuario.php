<?php
require_once '../controller/UsuarioController.php';

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new UsuarioController();

    $dados = [
        'nome' => $_POST['nome'],
        'email' => $_POST['email'],
        'senha' => $_POST['senha'],
        'cpf' => $_POST['cpf'],
        'data_nascimento' => $_POST['data_nascimento']
    ];

    $mensagem = $controller->cadastrar($dados);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
</head>
<body>
    <h1>Cadastro de Usuário</h1>

    <?php if ($mensagem != ""): ?>
        <p><strong><?php echo $mensagem; ?></strong></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <label>CPF:</label><br>
        <input type="text" name="cpf" required><br><br>

        <label>Data de Nascimento:</label><br>
        <input type="date" name="data_nascimento" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>

    <br>
    <a href="listar_usuarios.php">Ver usuários cadastrados</a>
</body>
</html>
