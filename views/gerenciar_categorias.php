<?php
require_once '../controller/CategoriaController.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$controller = new CategoriaController();
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    switch ($acao) {
        case 'cadastrar':
            $dados = ['nome' => $_POST['nome']];
            $mensagem = $controller->cadastrar($dados);
            break;
            
        case 'editar':
            $dados = [
                'id' => $_POST['id'],
                'nome' => $_POST['nome']
            ];
            $mensagem = $controller->atualizar($dados);
            break;
            
        case 'excluir':
            $id = $_POST['id'];

            if ($controller->verificarUso($id)) {
                $mensagem = "Não é possível excluir esta categoria pois existem produtos vinculados a ela.";
            } else {
                $mensagem = $controller->excluir($id);
            }
            break;
    }
}

$categorias = $controller->listar();

$categoriaEditando = null;
if (isset($_GET['editar'])) {
    $categoriaEditando = $controller->buscarPorId($_GET['editar']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Gerenciar Categorias</title>

<style>
    
    * {
        box-sizing: border-box;
    }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        min-height: 100vh;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 40px;
        color: #333;
    }

    .container {
        background: #fff;
        width: 100%;
        max-width: 900px;
        border-radius: 10px;
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        padding: 30px 40px 40px;
        margin-bottom: 40px;
    }

    .nav-links {
        margin-bottom: 25px;
        text-align: center;
    }
    .nav-links a {
        color: #2575fc;
        text-decoration: none;
        font-weight: 600;
        margin: 0 12px;
        transition: color 0.3s ease;
        font-size: 14px;
    }
    .nav-links a:hover {
        color: #6a11cb;
        text-decoration: underline;
    }

    h1 {
        text-align: center;
        color: #222;
        margin-bottom: 30px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    
    .alert {
        padding: 12px 20px;
        border-radius: 6px;
        margin-bottom: 25px;
        font-weight: 600;
        font-size: 15px;
    }
    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    .alert-error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

   
    .card {
        background: #fafafa;
        border-radius: 8px;
        padding: 25px 30px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 20px;
        font-weight: 700;
        color: #444;
    }
    .card h4 {
        margin-top: 0;
        margin-bottom: 15px;
        font-weight: 600;
        color: #555;
    }

    
    form.form-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }
    .form-group {
        flex: 1 1 300px;
        display: flex;
        flex-direction: column;
    }
    label {
        margin-bottom: 6px;
        font-weight: 600;
        color: #555;
        font-size: 14px;
    }
    input[type="text"] {
        padding: 12px 15px;
        border: 1.8px solid #ddd;
        border-radius: 6px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }
    input[type="text"]:focus {
        border-color: #6a11cb;
        outline: none;
        box-shadow: 0 0 8px rgba(106, 17, 203, 0.3);
    }

    
    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
        transition: background-color 0.3s ease;
        color: #fff;
        background-color: #2575fc;
        text-align: center;
        user-select: none;
    }
    .btn:hover {
        background-color: #6a11cb;
    }
    .btn-success {
        background-color: #28a745;
    }
    .btn-success:hover {
        background-color: #1e7e34;
    }
    .btn-danger {
        background-color: #dc3545;
    }
    .btn-danger:hover {
        background-color: #b02a37;
    }
    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    .btn-secondary:hover {
        background-color: #545b62;
    }
    .btn-sm {
        padding: 7px 14px;
        font-size: 13px;
        border-radius: 4px;
    }

    
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
        font-size: 15px;
    }
    thead tr {
        background-color: #2575fc;
        color: white;
        text-align: left;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
    }
    thead tr th {
        padding: 12px 15px;
    }
    tbody tr {
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }
    tbody tr:hover {
        background-color: #f0f0f5;
    }
    tbody tr td {
        padding: 14px 15px;
        vertical-align: middle;
    }

    /* Status */
    .categoria-em-uso {
        color: #856404;
        font-style: italic;
        font-weight: 600;
    }
    .disponivel {
        color: #6c757d;
        font-style: normal;
        font-weight: 600;
    }

   
    .actions {
        white-space: nowrap;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .actions form {
        margin: 0;
    }

    
    .card ul {
        padding-left: 20px;
        color: #555;
    }
    .card ul li {
        margin-bottom: 8px;
        font-size: 14px;
    }

    
    @media (max-width: 600px) {
        form.form-inline {
            flex-direction: column;
            align-items: stretch;
        }
        .form-group {
            flex: none;
        }
        .actions {
            flex-direction: column;
            gap: 6px;
        }
    }
</style>

</head>
<body>
<div class="container">
    <div class="nav-links">
        <a href="painel.php">← Voltar ao Painel</a> |
        <a href="listar_produtos.php">Ver Produtos</a> |
        <a href="logout.php">Sair</a>
    </div>

    <h1>Gerenciar Categorias</h1>

    <?php if ($mensagem != ""): ?>
        <div class="alert <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'alert-success' : 'alert-error'; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <!-- Formulário de Cadastro/Edição -->
    <div class="card">
        <h3><?php echo $categoriaEditando ? 'Editar Categoria' : 'Nova Categoria'; ?></h3>
        <form method="POST" class="form-inline" novalidate>
            <input type="hidden" name="acao" value="<?php echo $categoriaEditando ? 'editar' : 'cadastrar'; ?>">
            <?php if ($categoriaEditando): ?>
                <input type="hidden" name="id" value="<?php echo $categoriaEditando['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="nome">Nome da Categoria:</label>
                <input type="text" id="nome" name="nome" value="<?php echo $categoriaEditando ? htmlspecialchars($categoriaEditando['nome']) : ''; ?>" required>
            </div>
            
            <div class="form-group" style="flex: none;">
                <button type="submit" class="btn <?php echo $categoriaEditando ? 'btn-success' : ''; ?>">
                    <?php echo $categoriaEditando ? 'Salvar Alterações' : 'Cadastrar'; ?>
                </button>
                <?php if ($categoriaEditando): ?>
                    <a href="gerenciar_categorias.php" class="btn btn-secondary" style="margin-left: 10px;">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

   
    <div class="card">
        <h3>Categorias Cadastradas</h3>
        
        <?php if (!empty($categorias)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <?php $emUso = $controller->verificarUso($categoria['id']); ?>
                        <tr>
                            <td><?php echo $categoria['id']; ?></td>
                            <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                            <td>
                                <?php if ($emUso): ?>
                                    <span class="categoria-em-uso">Em uso</span>
                                <?php else: ?>
                                    <span class="disponivel">Disponível</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="?editar=<?php echo $categoria['id']; ?>" class="btn btn-sm">Editar</a>
                                
                                <?php if (!$emUso): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled title="Não é possível excluir - categoria em uso">Excluir</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center; color: #666; font-style: italic;">Nenhuma categoria cadastrada.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h4>ℹ️ Informações</h4>
        <ul>
            <li><strong>Categoria "Em uso":</strong> possui produtos vinculados e não pode ser excluída</li>
            <li><strong>Categoria "Disponível":</strong> não possui produtos vinculados e pode ser excluída</li>
            <li>Para excluir uma categoria em uso, primeiro remova ou altere a categoria dos produtos vinculados</li>
        </ul>
    </div>
</div>
</body>
</html>