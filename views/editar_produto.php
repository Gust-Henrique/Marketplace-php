<?php
require_once '../controller/ProdutoController.php';
require_once '../config/database.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$controller = new ProdutoController();
$mensagem = "";
$categorias = [];

// Buscar categorias para o select
try {
    $database = new Database();
    $conn = $database->getConnection();
    $stmt = $conn->prepare("SELECT * FROM categorias ORDER BY nome");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mensagem = "Erro ao carregar categorias: " . $e->getMessage();
}

// Buscar produto a ser editado
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $produto = $controller->buscarPorId($id);

    if (!$produto) {
        echo "Produto não encontrado.";
        exit;
    }
} else {
    echo "ID do produto não informado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Processar upload de imagem (se houver)
    $imagem = $produto['imagem']; // Manter imagem atual por padrão
    
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $target_dir = "../public/images/";
        $target_file = $target_dir . basename($_FILES["imagem"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Verificar se é uma imagem real
        $check = getimagesize($_FILES["imagem"]["tmp_name"]);
        if ($check !== false) {
            // Permitir apenas alguns formatos
            if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                    // Remover imagem antiga se existir
                    if (!empty($produto['imagem']) && file_exists($target_dir . $produto['imagem'])) {
                        unlink($target_dir . $produto['imagem']);
                    }
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
            'id' => $_POST['id'],
            'nome' => $_POST['nome'],
            'descricao' => $_POST['descricao'],
            'preco' => $_POST['preco'],
            'imagem' => $imagem,
            'categoria_id' => $_POST['categoria_id']
        ];

        $mensagem = $controller->atualizar($dados);
        
        // Atualizar dados do produto
        $produto = $controller->buscarPorId($id);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        textarea { height: 100px; resize: vertical; }
        .btn { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #0056b3; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { margin-right: 10px; text-decoration: none; color: #007bff; }
        .current-image { margin-top: 10px; }
        .current-image img { max-width: 200px; height: auto; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="listar_produtos.php">← Voltar à Lista</a> |
        <a href="painel.php">Painel</a> |
        <a href="logout.php">Sair</a>
    </div>

    <h1>Editar Produto</h1>

    <?php if ($mensagem != ""): ?>
        <div class="alert <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'alert-success' : 'alert-error'; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
        
        <div class="form-group">
            <label for="nome">Nome do Produto:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="preco">Preço (R$):</label>
            <input type="number" id="preco" name="preco" step="0.01" min="0" value="<?php echo $produto['preco']; ?>" required>
        </div>

        <div class="form-group">
            <label for="categoria_id">Categoria:</label>
            <select id="categoria_id" name="categoria_id" required>
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['id']; ?>" <?php echo ($categoria['id'] == $produto['categoria_id']) ? 'selected' : ''; ?>>
                        <?php echo $categoria['nome']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="imagem">Imagem do Produto:</label>
            <input type="file" id="imagem" name="imagem" accept="image/*">
            <?php if (!empty($produto['imagem'])): ?>
                <div class="current-image">
                    <p>Imagem atual:</p>
                    <img src="../public/images/<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn">Salvar Alterações</button>
    </form>
</body>
</html>