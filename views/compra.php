<?php
require_once '../controller/ProdutoController.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    exit('Produto não especificado.');
}

$produtoId   = (int) $_GET['id'];
$controller  = new ProdutoController();
$produto     = $controller->buscarPorId($produtoId);

if (!$produto) {
    exit('Produto não encontrado.');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Compra – <?php echo htmlspecialchars($produto['nome']); ?></title>
    <style>
        body{font-family:sans-serif;background:#f4f7f9;padding:40px;}
        .card{max-width:600px;margin:auto;background:#fff;padding:30px;border-radius:10px;
              box-shadow:0 4px 12px rgba(0,0,0,.1);}
        h1{margin:0 0 15px;font-size:24px;}
        .price{color:#28a745;font-size:22px;font-weight:bold;margin:15px 0;}
        .btn{background:#28a745;color:#fff;border:none;border-radius:6px;padding:12px 24px;
             font-size:16px;cursor:pointer;}
        .btn:hover{background:#218838;}
    </style>
</head>
<body>
<div class="card">
    <h1>Confirmar Compra</h1>

    <p><strong>Produto:</strong> <?php echo htmlspecialchars($produto['nome']); ?></p>
    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($produto['descricao']); ?></p>
    <p><strong>Categoria:</strong> <?php echo htmlspecialchars($produto['categoria_nome']); ?></p>
    <p><strong>Vendedor:</strong> <?php echo htmlspecialchars($produto['usuario_nome']); ?></p>

    <p class="price">Preço: R$
        <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>

    <form action="finalizar_compra.php" method="POST">
        <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
        <button type="submit" class="btn">Finalizar Compra</button>
    </form>
</div>
</body>
</html>