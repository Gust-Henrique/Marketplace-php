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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lista de Usuários</title>

    <!-- Font Awesome para ícones -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
        }

        /* Navbar limpa e alinhada com ícones */
        .nav-links {
          display: flex;
          gap: 24px;
          border-bottom: 2px solid #ddd;
          padding: 14px 0;
          font-weight: 600;
          font-family: Arial, sans-serif;
        }

        .nav-links a {
          color: #f0f2f5;
          text-decoration: none;
          display: flex;
          align-items: center;
          gap: 8px;
          padding: 6px 12px;
          border-radius: 6px;
          transition: background-color 0.3s ease;
        }

        .nav-links a:hover {
          background-color: #f0f2f5;
          color: #000;
        }

        .nav-links i {
          font-size: 16px;
        }

        /* Header e botões */
        .header-actions {
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
        }

        h1 { 
            color:#f0f2f5; 
            margin-bottom: 20px; 
            font-size: 28px; 
        }

        .btn-primary { 
            background-color: #007bff; 
            color: white; 
            padding: 10px 20px; 
            font-size: 14px; 
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover { 
            background-color: #0056b3; 
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

        /* Tabela estilizada */
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

        /* Botões de ação */
        .btn {
            padding: 6px 12px; 
            margin: 2px; 
            text-decoration: none; 
            border-radius: 4px; 
            font-size: 12px; 
            font-weight: 500; 
            display: inline-block; 
            transition: all 0.3s ease; 
            color: white;
        }
        .btn-edit { 
            background-color: #28a745; 
        }
        .btn-edit:hover { 
            background-color: #1e7e34; 
            transform: translateY(-1px); 
        }
        .btn-delete { 
            background-color: #dc3545; 
        }
        .btn-delete:hover { 
            background-color: #c82333; 
            transform: translateY(-1px); 
        }

        /* Empty state */
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

        /* Responsividade */
        @media (max-width: 768px) {
            body { margin: 10px; }
            .nav-links { 
                flex-direction: column; 
                gap: 12px; 
                border-bottom: none; 
                border-left: 2px solid #ddd; 
                padding-left: 12px; 
            }
            .nav-links a {
                padding: 10px 15px;
            }
            table { font-size: 14px; }
            th, td { padding: 10px 8px; }
            .header-actions { flex-direction: column; gap: 10px; }
            .btn { padding: 8px 12px; font-size: 11px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-links">
            <a href="painel.php" title="Voltar ao Painel">
                <i class="fas fa-arrow-left"></i> Painel
            </a>
            <a href="cadastrar_usuario.php" title="Cadastrar Novo Usuário">
                <i class="fas fa-user-plus"></i> Novo Usuário
            </a>
            <a href="listar_produtos.php" title="Ver Produtos">
                <i class="fas fa-box-open"></i> Produtos
            </a>
            <a href="logout.php" title="Sair do sistema">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </nav>

        <div class="header-actions">
            <h1>Lista de Usuários</h1>
            <a href="cadastrar_usuario.php" class="btn-primary">+ Cadastrar Usuário</a>
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
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['cpf']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($usuario['data_nascimento'])); ?></td>
                                <td>
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
                    <a href="cadastrar_usuario.php" class="btn-primary">Cadastrar Primeiro Usuário</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
