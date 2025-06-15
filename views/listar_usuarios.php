<?php
require_once '../controller/UsuarioController.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$controller = new UsuarioController();
$usuarios = $controller->listar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuários</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background-color: #f5f5f5; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
        }
        .nav-links { 
            margin-bottom: 20px; 
            padding: 15px; 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .nav-links a { 
            margin-right: 15px; 
            text-decoration: none; 
            color: #007bff; 
            font-weight: 500; 
        }
        .nav-links a:hover { 
            text-decoration: underline; 
        }
        
        h1 { 
            color: #333; 
            margin-bottom: 20px; 
            font-size: 28px; 
        }
        
        .table-container { 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            overflow: hidden; 
            margin-top: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            padding: 15px 12px; 
            text-align: left; 
            border-bottom: 1px solid #eee; 
        }
        th { 
            background-color: #f8f9fa; 
            font-weight: bold; 
            color: #333; 
            font-size: 14px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        tr:hover { 
            background-color: #f8f9fa; 
        }
        tr:last-child td { 
            border-bottom: none; 
        }
        
        .btn { 
            padding: 6px 12px; 
            margin: 2px; 
            text-decoration: none; 
            border-radius: 4px; 
            font-size: 12px; 
            font-weight: 500; 
            display: inline-block; 
            transition: all 0.3s ease; 
        }
        .btn-edit { 
            background-color: #28a745; 
            color: white; 
        }
        .btn-edit:hover { 
            background-color: #1e7e34; 
            transform: translateY(-1px); 
        }
        .btn-delete { 
            background-color: #dc3545; 
            color: white; 
        }
        .btn-delete:hover { 
            background-color: #c82333; 
            transform: translateY(-1px); 
        }
        .btn-primary { 
            background-color: #007bff; 
            color: white; 
            padding: 10px 20px; 
            font-size: 14px; 
        }
        .btn-primary:hover { 
            background-color: #0056b3; 
        }
        
        .actions { 
            white-space: nowrap; 
        }
        
        .empty-state { 
            text-align: center; 
            padding: 40px 20px; 
            color: #666; 
        }
        .empty-state h3 { 
            color: #999; 
            margin-bottom: 10px; 
        }
        .empty-state p { 
            margin-bottom: 20px; 
        }
        
        .user-info { 
            color: #666; 
            font-size: 14px; 
        }
        .user-id { 
            font-weight: bold; 
            color: #007bff; 
        }
        .user-name { 
            font-weight: 600; 
            color: #333; 
        }
        .user-email { 
            color: #666; 
        }
        .user-cpf { 
            font-family: monospace; 
            font-size: 13px; 
        }
        .user-date { 
            color: #666; 
            font-size: 13px; 
        }
        
        .header-actions { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
        }
        
        .stats { 
            background: white; 
            padding: 15px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            margin-bottom: 20px; 
            color: #666; 
            font-size: 14px; 
        }
        .stats strong { 
            color: #007bff; 
        }
        
        @media (max-width: 768px) {
            body { margin: 10px; }
            .nav-links { padding: 10px; }
            .nav-links a { display: block; margin: 5px 0; }
            table { font-size: 14px; }
            th, td { padding: 10px 8px; }
            .header-actions { flex-direction: column; gap: 10px; }
            .btn { padding: 8px 12px; font-size: 11px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-links">
            <a href="painel.php">← Voltar ao Painel</a>
            <a href="cadastrar_usuario.php">+ Cadastrar Novo Usuário</a>
            <a href="listar_produtos.php">Ver Produtos</a>
            <a href="logout.php">Sair</a>
        </div>

        <div class="header-actions">
            <h1>Lista de Usuários</h1>
            <a href="cadastrar_usuario.php" class="btn btn-primary">+ Cadastrar Usuário</a>
        </div>

        <div class="stats">
            <strong><?php echo count($usuarios); ?></strong> usuário(s) cadastrado(s) no sistema
        </div>

        <div class="table-container">
            <?php if (!empty($usuarios)) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>CPF</th>
                            <th>Data de Nascimento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario) : ?>
                            <tr>
                                <td class="user-id"><?php echo $usuario['id']; ?></td>
                                <td class="user-name"><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td class="user-email"><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td class="user-cpf"><?php echo htmlspecialchars($usuario['cpf']); ?></td>
                                <td class="user-date"><?php echo date('d/m/Y', strtotime($usuario['data_nascimento'])); ?></td>
                                <td class="actions">
                                    <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-edit">Editar</a>
                                    <a href="excluir_usuario.php?id=<?php echo $usuario['id']; ?>" 
                                       class="btn btn-delete" 
                                       onclick="return confirm('Tem certeza que deseja excluir o usuário <?php echo htmlspecialchars($usuario['nome']); ?>?\n\nEsta ação não pode ser desfeita.')">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="empty-state">
                    <h3>Nenhum usuário encontrado</h3>
                    <p>Não há usuários cadastrados no sistema ainda.</p>
                    <a href="cadastrar_usuario.php" class="btn btn-primary">Cadastrar Primeiro Usuário</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>