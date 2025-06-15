<?php
require_once(__DIR__ . '/../model/Produto.php');

class ProdutoController {

    public function cadastrar($dados) {
        $produto = new Produto();
        $produto->nome = $dados['nome'];
        $produto->descricao = $dados['descricao'];
        $produto->preco = $dados['preco'];
        $produto->imagem = $dados['imagem'];
        $produto->categoria_id = $dados['categoria_id'];
        $produto->usuario_id = $dados['usuario_id'];

        if ($produto->cadastrar()) {
            return "Produto cadastrado com sucesso!";
        } else {
            return "Erro ao cadastrar produto.";
        }
    }

    public function listar() {
        $produto = new Produto();
        return $produto->listar()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $produto = new Produto();
        return $produto->buscarPorId($id);
    }

    public function atualizar($dados) {
        $produto = new Produto();
        $produto->id = $dados['id'];
        $produto->nome = $dados['nome'];
        $produto->descricao = $dados['descricao'];
        $produto->preco = $dados['preco'];
        $produto->imagem = $dados['imagem'];
        $produto->categoria_id = $dados['categoria_id'];

        if ($produto->atualizar()) {
            return "Produto atualizado com sucesso!";
        } else {
            return "Erro ao atualizar produto.";
        }
    }

    public function excluir($id) {
        $produto = new Produto();
        if ($produto->excluir($id)) {
            return "Produto excluído com sucesso!";
        } else {
            return "Erro ao excluir produto.";
        }
    }
}
?>