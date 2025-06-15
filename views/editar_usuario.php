<?php
require_once '../controller/UsuarioController.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$controller = new UsuarioController();
$mensagem = "";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $usuario = $controller->buscarPorId($id);

    if (!$usuario) {
        echo "Usuário não encontrado.";
        exit;
    }
} else {
    echo "ID não informado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'id' => $_POST['id'],
        'nome' => $_POST['nome'],
        'email' => $_POST['email'],
        'cpf' => $_POST['cpf'],
        'data_nascimento' => $_POST['data_nascimento']
    ];

    $mensagem = $controller->editar($dados);

    $usuario = $controller->buscarPorId($id);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
</head>
<body>
    <h1>Editar Usuário</h1>

    <a href="listar_usuarios.php">Voltar à lista</a>
    <br><br>

    <?php if ($mensagem != ""): ?>
        <p><strong><?php echo $mensagem; ?></strong></p>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?php echo $usuario['nome']; ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required><br><br>

        <label>CPF:</label><br>
        <input type="text" name="cpf" value="<?php echo $usuario['cpf']; ?>" required><br><br>

        <label>Data de Nascimento:</label><br>
        <input type="date" name="data_nascimento" value="<?php echo $usuario['data_nascimento']; ?>" required><br><br>

        <input type="submit" value="Salvar Alterações">
    </form>
</body>
</html>
