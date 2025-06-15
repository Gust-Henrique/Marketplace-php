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

    <style>
        /* ====== RESET BÁSICO ====== */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* ====== LAYOUT GERAL ====== */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: #fff;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }

        /* ====== TÍTULO / HEADER ====== */
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .header h1 {
            font-size: 2rem;
            margin-bottom: .5rem;
        }
        .header p {
            color: #666;
            font-size: 1rem;
        }

        /* ====== LINK DE VOLTAR ====== */
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 18px;
            background: rgba(102,126,234,.15);
            border-radius: 20px;
            transition: all .3s ease;
        }
        .back-link:hover {
            background: rgba(102,126,234,.25);
            transform: translateY(-2px);
        }

        /* ====== ALERTAS ====== */
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .alert-error   { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

        /* ====== FORMULÁRIO ====== */
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            margin-bottom: .5rem;
            font-weight: 600;
            color:#333;
            font-size:.95rem;
        }
        .form-group input {
            width: 100%;
            padding: .875rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all .3s ease;
            background:#f8f9fa;
        }
        .form-group input:focus {
            outline:none;
            border-color:#667eea;
            background:#fff;
            box-shadow:0 0 0 3px rgba(102,126,234,.1);
        }

        /* ====== BOTÕES ====== */
        .btn {
            width:100%;
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            color:#fff;
            border:none;
            padding:1rem;
            border-radius:8px;
            font-size:1.1rem;
            font-weight:600;
            cursor:pointer;
            transition: all .3s ease;
        }
        .btn:hover    { transform:translateY(-2px); box-shadow:0 8px 25px rgba(102,126,234,.3); }
        .btn:active   { transform:translateY(0); }

        /* ====== RESPONSIVO ====== */
        @media(max-width:480px){
            .container { padding:2rem; margin:10px; }
            .header h1 { font-size:1.7rem; }
            .back-link { margin-bottom:15px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Editar Usuário</h1>
            <p>Atualize as informações abaixo</p>
        </div>

        <!-- Link de voltar -->
        <a href="painel.php" class="back-link">← Voltar ao Painel</a>

        <!-- Mensagem flash (sucesso ou erro) -->
        <?php if ($mensagem != ""): ?>
            <div class="alert <?php echo (strpos($mensagem,'sucesso')!==false) ? 'alert-success' : 'alert-error'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <!-- Formulário -->
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

            <div class="form-group">
                <label for="nome">Nome</label>
                <input id="nome" type="text" name="nome" value="<?php echo $usuario['nome']; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input id="email" type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
            </div>

            <div class="form-group">
                <label for="cpf">CPF</label>
                <input id="cpf" type="text" name="cpf" value="<?php echo $usuario['cpf']; ?>" required>
            </div>

            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento</label>
                <input id="data_nascimento" type="date" name="data_nascimento" value="<?php echo $usuario['data_nascimento']; ?>" required>
            </div>

            <button type="submit" class="btn">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>
