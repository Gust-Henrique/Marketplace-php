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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { margin-right: 10px; text-decoration: none; color: #007bff; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background-color: #0056b3; }
        .btn-success { background-color: #28a745; }
        .btn-success:hover { background-color: #1e7e34; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #545b62; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-inline { display: flex; gap: 10px; align-items: end; }
        .form-inline .form-group { flex: 1; }
        .categoria-em-uso { color: #856404; font-style: italic; }
        .actions { white-space: nowrap; }
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
            <form method="POST" class="form-inline">
                <input type="hidden" name="acao" value="<?php echo $categoriaEditando ? 'editar' : 'cadastrar'; ?>">
                <?php if ($categoriaEditando): ?>
                    <input type="hidden" name="id" value="<?php echo $categoriaEditando['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nome">Nome da Categoria:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo $categoriaEditando ? htmlspecialchars($categoriaEditando['nome']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn <?php echo $categoriaEditando ? 'btn-success' : 'btn'; ?>">
                        <?php echo $categoriaEditando ? 'Salvar Alterações' : 'Cadastrar'; ?>
                    </button>
                    <?php if ($categoriaEditando): ?>
                        <a href="gerenciar_categorias.php" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Lista de Categorias -->
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
                                        <span style="color: #6c757d;">Disponível</span>
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
                <p>Nenhuma categoria cadastrada.</p>
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