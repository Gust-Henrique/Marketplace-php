<?php
require_once '../controller/CompraController.php';
require_once '../controller/ProdutoController.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_POST['produto_id'])) {
    exit('Produto não informado.');
}

$produtoId  = (int) $_POST['produto_id'];
$comprador  = (int) $_SESSION['usuario_id'];

$prodCtrl   = new ProdutoController();
$produto    = $prodCtrl->buscarPorId($produtoId);
if (!$produto) {
    exit('Produto inexistente.');
}

$compraCtrl = new CompraController();
$sucesso    = $compraCtrl->criar($produtoId, $comprador);

if ($sucesso) {
    ?>
    <!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8">
    <title>Compra realizada</title>
    <style>*{font-family:sans-serif}body{background:#f4f7f9;padding:40px;text-align:center}
        .box{background:#fff;max-width:500px;margin:auto;padding:40px;border-radius:10px;
             box-shadow:0 4px 12px rgba(0,0,0,.1)}</style></head><body>
    <div class="box">
        <h1>Compra realizada com sucesso! 🎉</h1>
        <p><strong>Produto:</strong> <?php echo htmlspecialchars($produto['nome']); ?></p>
        <p><strong>Valor:</strong> R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
        <p>O vendedor receberá a notificação do seu pedido.</p>   
        <a href="../index.php">← Voltar à loja</a>
    </div>
    </body></html>
    <?php
} else {
    exit('Falha ao salvar compra.');
}