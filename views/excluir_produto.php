<?php
require_once '../controller/ProdutoController.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $controller = new ProdutoController();
    $produto = $controller->buscarPorId($id);
    
    if ($produto) {
        if (!empty($produto['imagem'])) {
            $caminho_imagem = "../public/images/" . $produto['imagem'];
            if (file_exists($caminho_imagem)) {
                unlink($caminho_imagem);
            }
        }
        
        $mensagem = $controller->excluir($id);
        
        $_SESSION['mensagem'] = $mensagem;
        header('Location: listar_produtos.php');
        exit;
    } else {
        $_SESSION['mensagem'] = "Produto não encontrado.";
        header('Location: listar_produtos.php');
        exit;
    }
} else {
    $_SESSION['mensagem'] = "ID do produto não informado.";
    header('Location: listar_produtos.php');
    exit;
}
?>