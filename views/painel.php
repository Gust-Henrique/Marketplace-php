<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM usuarios");
    $stmt->execute();
    $totalUsuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM produtos");
    $stmt->execute();
    $totalProdutos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM categorias");
    $stmt->execute();
    $totalCategorias = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM produtos WHERE usuario_id = :usuario_id");
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
    $stmt->execute();
    $meusProdutos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
} catch (Exception $e) {
    $totalUsuarios = $totalProdutos = $totalCategorias = $meusProdutos = 0;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Marketplace</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --success-color: #10b981;
            --success-hover: #059669;
            --danger-color: #ef4444;
            --danger-hover: #dc2626;
            --secondary-color: #6b7280;
            --secondary-hover: #4b5563;
            --background: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 12px;
            --border-radius-sm: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-simples {
            background: var(--card-background);
            padding: 24px 32px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .titulo {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .botoes {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius-sm);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-marketplace {
            background: var(--primary-color);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-marketplace:hover {
            background: var(--primary-hover);
        }

        .btn-sair {
            background: var(--danger-color);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-sair:hover {
            background: var(--danger-hover);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-success:hover {
            background: var(--success-hover);
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-secondary:hover {
            background: var(--secondary-hover);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-primary);
            border: 2px solid var(--secondary-color);
        }

        .btn-outline:hover {
            background: var(--secondary-color);
            color: white;
        }

        .user-info {
            background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.9));
            padding: 20px 32px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            text-align: center;
            color: var(--text-primary);
            font-size: 1.125rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card-background);
            padding: 28px 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            position: relative;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 12px;
            color: var(--primary-color);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
        }

        .action-card {
            background: var(--card-background);
            padding: 32px 28px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            position: relative;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .action-card h3 {
            color: var(--text-primary);
            margin-bottom: 12px;
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .action-card p {
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 0.925rem;
        }

        .action-card .btn {
            margin-right: 12px;
            margin-bottom: 12px;
        }

        .action-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
        }



        
        @media (max-width: 768px) {
            body {
                padding: 12px;
            }

            .header-simples {
                flex-direction: column;
                gap: 20px;
                text-align: center;
                padding: 20px;
            }

            .titulo {
                font-size: 1.5rem;
            }

            .botoes {
                flex-wrap: wrap;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px;
            }

            .actions-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .stat-card, .action-card {
                padding: 20px;
            }

            .btn {
                padding: 10px 16px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .stat-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="header-simples">
            <h1 class="titulo">Painel Administrativo</h1>
            <div class="botoes">
                <a href="../index.php" class="btn btn-marketplace">
                    <i class="fas fa-store"></i>
                    Voltar ao Marketplace
                </a>
                <a href="logout.php" class="btn btn-sair" onclick="return confirm('Deseja sair?')">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </div>
        </div>

        
        <div class="user-info">
            <i class="fas fa-user-circle" style="font-size: 1.5rem; margin-right: 12px; color: var(--primary-color);"></i>
            Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</strong>
        </div>

        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $totalUsuarios; ?></div>
                <div class="stat-label">Usuários Cadastrados</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-number"><?php echo $totalProdutos; ?></div>
                <div class="stat-label">Produtos no Sistema</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-number"><?php echo $totalCategorias; ?></div>
                <div class="stat-label">Categorias Disponíveis</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-number"><?php echo $meusProdutos; ?></div>
                <div class="stat-label">Meus Produtos</div>
            </div>
        </div>

        
        <div class="actions-grid">
            <div class="action-card">
                <h3>
                    <i class="fas fa-box action-icon"></i>
                    Gerenciar Produtos
                </h3>
                <p>Cadastre novos produtos, edite informações ou visualize todos os produtos do marketplace.</p>
                <a href="cadastrar_produto.php" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    Cadastrar Produto
                </a>
                <a href="listar_produtos.php" class="btn btn-outline">
                    <i class="fas fa-list"></i>
                    Ver Todos
                </a>
            </div>

            <div class="action-card">
                <h3>
                    <i class="fas fa-users action-icon"></i>
                    Gerenciar Usuários
                </h3>
                <p>Visualize usuários cadastrados, edite informações ou cadastre novos usuários.</p>
                <a href="cadastrar_usuario.php" class="btn btn-success">
                    <i class="fas fa-user-plus"></i>
                    Cadastrar Usuário
                </a>
                <a href="listar_usuarios.php" class="btn btn-outline">
                    <i class="fas fa-list"></i>
                    Ver Todos
                </a>
            </div>

            <div class="action-card">
                <h3>
                    <i class="fas fa-tags action-icon"></i>
                    Categorias
                </h3>
                <p>Organize seus produtos criando e gerenciando categorias.</p>
                <a href="gerenciar_categorias.php" class="btn btn-outline">
                    <i class="fas fa-cog"></i>
                    Gerenciar Categorias
                </a>
            </div>

            <div class="action-card">
                <h3>
                    <i class="fas fa-user-cog action-icon"></i>
                    Meu Perfil
                </h3>
                <p>Edite suas informações pessoais e gerencie sua conta.</p>
                <a href="editar_usuario.php?id=<?php echo $_SESSION['usuario_id']; ?>" class="btn btn-secondary">
                    <i class="fas fa-edit"></i>
                    Editar Perfil
                </a>
            </div>
        </div>
    </div>
</body>
</html>