<?php
require_once '../controller/ProdutoController.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$controller = new ProdutoController();
$produtos = $controller->listar();

$mensagem = "";
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lista de Produtos</title>
    <style>
        /* Reset e base */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Navbar moderna e limpa */
        .nav-links {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 12px;
            border-bottom: 1px solid #ddd;
            font-weight: 600;
            font-size: 15px;
        }

        .nav-links a {
            color: #f0f2f5;
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 5px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .nav-links a:hover {
            background-color:rgb(0, 4, 255);
            color: #fff;
        }

        /* Título */
        h1 {
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 28px;
            color: #222;
        }

        /* Alertas */
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 14px;
        }
        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }

        /* Tabela com cantos arredondados e sombra sutil */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            background: #fff;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.08);
            border-radius: 8px;
            overflow: hidden;
            font-size: 14px;
        }

        thead tr {
            background-color: #f0f2f5;
            color: #555;
            font-weight: 700;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            vertical-align: middle;
        }

        tbody tr {
            background: #fff;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);
            border-radius: 6px;
        }

        tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Imagem do produto */
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgb(0 0 0 / 0.1);
        }
        .no-image {
            width: 50px;
            height: 50px;
            background-color: #e2e6ea;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 11px;
            font-weight: 600;
            user-select: none;
            box-shadow: inset 0 1px 3px rgb(0 0 0 / 0.05);
        }

        /* Preço destacado */
        .price {
            font-weight: 700;
            color: #198754;
        }

        /* Botões estilo flat e com cores sutis */
        .btn {
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.25s ease;
            cursor: pointer;
            border: none;
        }

        .btn-edit {
            background-color: #0d6efd;
            color: white;
        }
        .btn-edit:hover {
            background-color: #084cd6;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background-color: #a71d2a;
        }

        /* Link no empty state */
        td[colspan] a {
            color: #0d6efd;
            text-decoration: underline;
            font-weight: 600;
        }

        /* Responsividade simples */
        @media (max-width: 720px) {
            .nav-links {
                flex-wrap: wrap;
                gap: 12px;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                display: none;
            }

            tbody tr {
                margin-bottom: 20px;
                box-shadow: none;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 12px;
            }

            tbody td {
                padding: 8px 12px;
                text-align: right;
                position: relative;
                border: none;
                border-bottom: 1px solid #eee;
            }

            tbody td:last-child {
                border-bottom: 0;
            }

            tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 12px;
                top: 12px;
                font-weight: 700;
                color: #555;
                text-transform: uppercase;
                font-size: 11px;
            }

            .product-image, .no-image {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <nav class="nav-links">
        <a href="painel.php">← Voltar ao Painel</a>
        <a href="cadastrar_produto.php">+ Cadastrar Produto</a>
        <a href="gerenciar_categorias.php">Gerenciar Categorias</a>
        <a href="listar_usuarios.php">Ver Usuários</a>
        <a href="logout.php">Sair</a>
    </nav>

    <h1>Lista de Produtos</h1>

    <?php if ($mensagem != ""): ?>
        <div class="alert <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'alert-success' : 'alert-error'; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

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
                        <td data-label="ID"><?php echo $produto['id']; ?></td>
                        <td data-label="Imagem">
                            <?php if (!empty($produto['imagem'])): ?>
                                <img src="../public/images/<?php echo $produto['imagem']; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="product-image" />
                            <?php else: ?>
                                <div class="no-image">Sem imagem</div>
                            <?php endif; ?>
                        </td>
                        <td data-label="Nome"><?php echo htmlspecialchars($produto['nome']); ?></td>
                        <td data-label="Descrição"><?php echo htmlspecialchars(mb_strimwidth($produto['descricao'], 0, 100, '...')); ?></td>
                        <td data-label="Preço" class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                        <td data-label="Categoria"><?php echo htmlspecialchars($produto['categoria_nome'] ?? 'Sem categoria'); ?></td>
                        <td data-label="Vendedor"><?php echo htmlspecialchars($produto['usuario_nome'] ?? 'Usuário removido'); ?></td>
                        <td data-label="Ações">
                            <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-edit">Editar</a>
                            <a href="excluir_produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding: 30px 0; color: #666;">
                        Nenhum produto encontrado. <a href="cadastrar_produto.php">Cadastrar primeiro produto</a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>