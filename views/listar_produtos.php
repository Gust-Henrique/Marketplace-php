<?php
require_once '../controller/ProdutoController.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$controller = new ProdutoController();
$produtos = $controller->listar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { margin-right: 10px; text-decoration: none; color: #007bff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn { padding: 5px 10px; margin: 2px; text-decoration: none; border-radius: 3px; font-size: 12px; }
        .btn-edit { background-color: #28a745; color: white; }
        .btn-delete { background-color: #dc3545; color: white; }
        .btn:hover { opacity: 0.8; }
        .product-image { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
        .price { font-weight: bold; color: #28a745; }
        .no-image { width: 50px; height: 50px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 4px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="painel.php">← Voltar ao Painel</a> |
        <a href="cadastrar_produto.php">+ Cadastrar Produto</a> |
        <a href="listar_usuarios.php">Ver Usuários</a> |
        <a href="logout.php">Sair</a>
    </div>

    <h1>Lista de Produtos</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Categoria</th>
                <th>Vendedor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($produtos)) : ?>
                <?php foreach ($produtos as $produto) : ?>
                    <tr>
                        <td><?php echo $produto['id']; ?></td>
                        <td>
                            <?php if (!empty($produto['imagem'])): ?>
                                <img src="../public/images/<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>" class="product-image">
                            <?php else: ?>
                                <div class="no-image">Sem imagem</div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                        <td><?php echo htmlspecialchars(substr($produto['descricao'], 0, 100)) . (strlen($produto['descricao']) > 100 ? '...' : ''); ?></td>
                        <td class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($produto['categoria_nome'] ?? 'Sem categoria'); ?></td>
                        <td><?php echo htmlspecialchars($produto['usuario_nome'] ?? 'Usuário removido'); ?></td>
                        <td>
                            <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-edit">Editar</a>
                            <a href="excluir_produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #666;">
                        Nenhum produto encontrado. <a href="cadastrar_produto.php">Cadastrar primeiro produto</a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>