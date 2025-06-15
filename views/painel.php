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
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background-color: #f5f5f5; 
        }
        .header { 
            background-color: #007bff; 
            color: white; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 30px; 
        }
        .header h1 { margin: 0; }
        .header .user-info { margin-top: 10px; font-size: 14px; opacity: 0.9; }
        
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .stat-card { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            text-align: center; 
        }
        .stat-number { 
            font-size: 32px; 
            font-weight: bold; 
            color: #007bff; 
            margin: 10px 0; 
        }
        .stat-label { 
            color: #666; 
            font-size: 14px; 
        }
        
        .actions-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
        }
        .action-card { 
            background: white; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .action-card h3 { 
            margin-top: 0; 
            color: #333; 
        }
        .action-card p { 
            color: #666; 
            margin: 10px 0 15px 0; 
        }
        .btn { 
            display: inline-block; 
            padding: 10px 20px; 
            background-color: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px; 
            transition: background-color 0.3s; 
        }
        .btn:hover { 
            background-color: #0056b3; 
        }
        .btn-secondary { 
            background-color: #6c757d; 
        }
        .btn-secondary:hover { 
            background-color: #545b62; 
        }
        .btn-success { 
            background-color: #28a745; 
        }
        .btn-success:hover { 
            background-color: #1e7e34; 
        }
        
        .logout { 
            position: absolute; 
            top: 20px; 
            right: 20px; 
        }
        .logout a { 
            color: #dc3545; 
            text-decoration: none; 
            font-weight: bold; 
        }
        .logout a:hover { 
            text-decoration: underline; 
        }
    </style>
</head>
<body>
    <div class="logout">
        <a href="logout.php">Sair →</a>
    </div>

    <div class="header">
        <h1>Marketplace</h1>
        <div class="user-info">
            Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</strong>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $totalUsuarios; ?></div>
            <div class="stat-label">Usuários Cadastrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $totalProdutos; ?></div>
            <div class="stat-label">Produtos no Sistema</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $totalCategorias; ?></div>
            <div class="stat-label">Categorias Disponíveis</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $meusProdutos; ?></div>
            <div class="stat-label">Meus Produtos</div>
        </div>
    </div>

    <div class="actions-grid">
        <div class="action-card">
            <h3>Gerenciar Produtos</h3>
            <p>Cadastre novos produtos, edite informações ou visualize todos os produtos do marketplace.</p>
            <a href="cadastrar_produto.php" class="btn btn-success">+ Cadastrar Produto</a>
            <a href="listar_produtos.php" class="btn">Ver Todos</a>
        </div>

        <div class="action-card">
            <h3>Gerenciar Usuários</h3>
            <p>Visualize usuários cadastrados, edite informações ou cadastre novos usuários.</p>
            <a href="cadastrar_usuario.php" class="btn btn-success">+ Cadastrar Usuário</a>
            <a href="listar_usuarios.php" class="btn">Ver Todos</a>
        </div>

        <div class="action-card">
            <h3>Categorias</h3>
            <p>Organize seus produtos criando e gerenciando categorias.</p>
            <a href="gerenciar_categorias.php" class="btn">Gerenciar Categorias</a>
        </div>

        <div class="action-card">
            <h3>Meu Perfil</h3>
            <p>Edite suas informações pessoais e gerencie sua conta.</p>
            <a href="editar_usuario.php?id=<?php echo $_SESSION['usuario_id']; ?>" class="btn btn-secondary">Editar Perfil</a>
        </div>
    </div>
</body>
</html>