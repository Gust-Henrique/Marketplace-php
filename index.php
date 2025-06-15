<?php
session_start();
require_once 'config/config.php';
require_once 'controller/ProdutoController.php';
require_once 'model/Categoria.php';

$produtoController = new ProdutoController();
$categoriaModel = new Categoria();

$produtos = $produtoController->listar();
$categorias = $categoriaModel->listar()->fetchAll(PDO::FETCH_ASSOC);

$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

if ($categoria_filtro) {
    $produto = new Produto();
    $stmt = $produto->buscarPorCategoria($categoria_filtro);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $produtos = $produtoController->listar();
}

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);
$nome_usuario = $usuario_logado ? $_SESSION['usuario_nome'] : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - Encontre o que procura</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .header h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .auth-section {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.1);
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
        }

        .auth-links a, .btn-painel, .btn-logout {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
            display: inline-block;
        }

        .auth-links a:hover, .btn-painel:hover, .btn-logout:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        .btn-painel {
            background: rgba(40, 167, 69, 0.8);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-painel:hover {
            background: rgba(40, 167, 69, 1);
        }

        .btn-logout {
            background: rgba(220, 53, 69, 0.8);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-logout:hover {
            background: rgba(220, 53, 69, 1);
        }

        .filters {
            background: white;
            padding: 2rem;
            margin: 2rem 0;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .filters h3 {
            margin-bottom: 1rem;
            color: #667eea;
        }

        .filter-row {
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
        }

        .no-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 1.1rem;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-name {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .product-description {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 1rem;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #666;
        }

        .category-tag {
            background: #667eea;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .no-products {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .no-products h3 {
            margin-bottom: 1rem;
        }

        .footer {
            background: #343a40;
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .auth-section {
                position: static;
                justify-content: center;
                margin-top: 1rem;
                flex-wrap: wrap;
            }

            .user-info {
                order: -1;
                margin-bottom: 10px;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .filter-group {
                min-width: 100%;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .auth-section {
                flex-direction: column;
                gap: 10px;
            }

            .user-info {
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="auth-section">
            <?php if ($usuario_logado): ?>
                <?php
                    // Limita o nome a no máximo 4 caracteres + "..."
                    $display_nome = (mb_strlen($nome_usuario, 'UTF-8') > 4)
                        ? mb_substr($nome_usuario, 0, 4, 'UTF-8') . '...'
                        : $nome_usuario;
                ?>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(mb_substr($nome_usuario, 0, 1, 'UTF-8')); ?>
                    </div>
                    <span class="user-name">Olá, <?php echo htmlspecialchars($display_nome, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <a href="views/painel.php" class="btn-painel">📋 Meu Painel</a>
                <a href="views/logout.php" class="btn-logout">🚪 Sair</a>
            <?php else: ?>
                <div class="auth-links">
                    <a href="views/login.php">Entrar</a>
                    <a href="views/cadastrar_usuario.php">Cadastrar</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="container">
            <h1>🛍️ Marketplace</h1>
            <p>Encontre os melhores produtos com os melhores preços</p>
        </div>
    </div>

    <div class="container">
        <div class="filters">
            <h3>🔍 Buscar Produtos</h3>
            <form method="GET" class="filter-row">
                <div class="filter-group">
                    <label for="busca">Pesquisar por nome:</label>
                    <input type="text" id="busca" name="busca" value="<?php echo htmlspecialchars($busca); ?>" placeholder="Digite o nome do produto...">
                </div>
                <div class="filter-group">
                    <label for="categoria">Categoria:</label>
                    <select id="categoria" name="categoria">
                        <option value="">Todas as categorias</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>" <?php echo ($categoria_filtro == $categoria['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn">Buscar</button>
                </div>
            </form>
        </div>

        <div class="products-grid">
            <?php if (!empty($produtos)): ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="product-card">
                        <?php if (!empty($produto['imagem'])): ?>
                            <img src="public/images/<?php echo $produto['imagem']; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="product-image">
                        <?php else: ?>
                            <div class="no-image">📦 Sem imagem</div>
                        <?php endif; ?>
                        
                        <div class="product-info">
                            <div class="product-name"><?php echo htmlspecialchars($produto['nome']); ?></div>
                            <div class="product-description">
                                <?php echo htmlspecialchars(substr($produto['descricao'], 0, 100)) . (strlen($produto['descricao']) > 100 ? '...' : ''); ?>
                            </div>
                            <div class="product-price"><?php echo formatarPreco($produto['preco']); ?></div>
                            <div class="product-meta">
                                <span class="category-tag"><?php echo htmlspecialchars($produto['categoria_nome'] ?? 'Sem categoria'); ?></span>
                                <span>Por: <?php echo htmlspecialchars($produto['usuario_nome'] ?? 'Usuário'); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <h3>🔍 Nenhum produto encontrado</h3>
                    <p>Não encontramos produtos que correspondam aos seus critérios de busca.</p>
                    <p>Tente:</p>
                    <ul style="text-align: left; display: inline-block; margin-top: 1rem;">
                        <li>Verificar a ortografia das palavras-chave</li>
                        <li>Usar termos mais gerais</li>
                        <li>Experimentar diferentes categorias</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; 2025 Marketplace. Todos os direitos reservados.</p>
            <p>Conectando vendedores e compradores de forma simples e segura.</p>
        </div>
    </div>
</body>
</html>