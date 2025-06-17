<?php
require_once '../controller/ProdutoController.php';
require_once '../config/database.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensagem = "";
$categorias = [];

try {
    $database = new Database();
    $conn = $database->getConnection();
    $stmt = $conn->prepare("SELECT * FROM categorias ORDER BY nome");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mensagem = "Erro ao carregar categorias: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new ProdutoController();

    $imagem = "";
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $target_dir = "../public/images/";
        $target_file = $target_dir . basename($_FILES["imagem"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["imagem"]["tmp_name"]);
        if ($check !== false) {
            if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                    $imagem = basename($_FILES["imagem"]["name"]);
                } else {
                    $mensagem = "Erro ao fazer upload da imagem.";
                }
            } else {
                $mensagem = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
            }
        } else {
            $mensagem = "O arquivo não é uma imagem válida.";
        }
    }

    if ($mensagem == "") {
        $dados = [
            'nome' => $_POST['nome'],
            'descricao' => $_POST['descricao'],
            'preco' => $_POST['preco'],
            'imagem' => $imagem,
            'categoria_id' => $_POST['categoria_id'],
            'usuario_id' => $_SESSION['usuario_id']
        ];

        $mensagem = $controller->cadastrar($dados);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto - Marketplace</title>
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
            max-width: 900px;
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
            display: flex;
            align-items: center;
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
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-secondary:hover {
            background: var(--secondary-hover);
            transform: translateY(-1px);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-success:hover {
            background: var(--success-hover);
            transform: translateY(-1px);
        }

        .nav-links {
            background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.9));
            padding: 20px 32px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: var(--primary-color);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: var(--border-radius-sm);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            border: 2px solid transparent;
        }

        .nav-links a:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }

        .form-container {
            background: var(--card-background);
            padding: 32px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .alert {
            padding: 16px 20px;
            margin-bottom: 24px;
            border-radius: var(--border-radius-sm);
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: var(--success-color);
            color: white;
        }

        .alert-error {
            background: var(--danger-color);
            color: white;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.925rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: var(--border-radius-sm);
            font-size: 0.925rem;
            transition: all 0.2s ease;
            background: #f9fafb;
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        .file-input-wrapper {
            position: relative;
            display: block;
            width: 100%;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-display {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 24px;
            border: 2px dashed #d1d5db;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f9fafb;
            min-height: 120px;
            flex-direction: column;
        }

        .file-input-display:hover {
            border-color: var(--primary-color);
            background: rgba(79, 70, 229, 0.05);
        }

        .file-input-display i {
            font-size: 2rem;
            color: var(--secondary-color);
        }

        .file-input-display span {
            color: var(--text-secondary);
            font-weight: 500;
            text-align: center;
        }

        .price-input {
            position: relative;
        }

        .price-input::before {
            content: 'R$';
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-weight: 600;
            z-index: 1;
        }

        .price-input input {
            padding-left: 50px;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            font-size: 1rem;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            body {
                padding: 12px;
            }

            .header-simples {
                flex-direction: column;
                gap: 16px;
                text-align: center;
                padding: 20px;
            }

            .titulo {
                font-size: 1.5rem;
            }

            .nav-links {
                justify-content: center;
                padding: 16px;
            }

            .form-container {
                padding: 24px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .btn {
                padding: 10px 16px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .nav-links {
                flex-direction: column;
                align-items: center;
            }
            
            .nav-links a {
                width: 100%;
                justify-content: center;
            }
        }

        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>

    <div class="container">
        <div class="header-simples">
            <h1 class="titulo">
                <i class="fas fa-plus-circle"></i>
                Cadastrar Produto
            </h1>
        </div>

        <div class="nav-links">
            <a href="painel.php">
                <i class="fas fa-arrow-left"></i>
                Voltar ao Painel
            </a>
            <a href="listar_produtos.php">
                <i class="fas fa-list"></i>
                Ver Produtos
            </a>
            <a href="../index.php">
                <i class="fas fa-store"></i>
                Marketplace
            </a>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Sair
            </a>
        </div>

        <div class="form-container">
            <?php if ($mensagem != ""): ?>
                <div class="alert <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'alert-success' : 'alert-error'; ?>">
                    <i class="fas <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" id="productForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome">
                            <i class="fas fa-tag"></i>
                            Nome do Produto
                        </label>
                        <input type="text" id="nome" name="nome" required placeholder="Digite o nome do produto">
                    </div>

                    <div class="form-group">
                        <label for="categoria_id">
                            <i class="fas fa-folder"></i>
                            Categoria
                        </label>
                        <select id="categoria_id" name="categoria_id" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nome']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="preco">
                            <i class="fas fa-dollar-sign"></i>
                            Preço
                        </label>
                        <div class="price-input">
                            <input type="number" id="preco" name="preco" step="0.01" min="0" required placeholder="0,00">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="imagem">
                            <i class="fas fa-image"></i>
                            Imagem do Produto
                        </label>
                        <div class="file-input-wrapper">
                            <input type="file" class="file-input" id="imagem" name="imagem" accept="image/*">
                            <div class="file-input-display">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Clique para selecionar uma imagem<br>ou arraste e solte aqui</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="descricao">
                            <i class="fas fa-align-left"></i>
                            Descrição
                        </label>
                        <textarea id="descricao" name="descricao" placeholder="Descreva detalhadamente o produto..."></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success submit-btn">
                    <i class="fas fa-save"></i>
                    Cadastrar Produto
                </button>
            </form>
        </div>
    </div>

    <script>
        
        document.getElementById('imagem').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const display = document.querySelector('.file-input-display');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    display.innerHTML = `
                        <img src="${e.target.result}" style="max-width: 80px; max-height: 80px; border-radius: 8px; object-fit: cover;">
                        <span style="font-size: 0.85rem;">Imagem selecionada: ${file.name}</span>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });

        
        document.getElementById('productForm').addEventListener('submit', function() {
            document.getElementById('loading').style.display = 'flex';
        });

        
        document.getElementById('preco').addEventListener('input', function(e) {
            let value = e.target.value;
            if (value < 0) {
                e.target.value = 0;
            }
        });

        
        const fileInputDisplay = document.querySelector('.file-input-display');
        const fileInput = document.getElementById('imagem');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileInputDisplay.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileInputDisplay.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileInputDisplay.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            fileInputDisplay.style.borderColor = 'var(--primary-color)';
            fileInputDisplay.style.background = 'rgba(79, 70, 229, 0.1)';
        }

        function unhighlight(e) {
            fileInputDisplay.style.borderColor = '#d1d5db';
            fileInputDisplay.style.background = '#f9fafb';
        }

        fileInputDisplay.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            
            
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    </script>
</body>
</html>