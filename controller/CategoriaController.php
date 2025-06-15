<?php
require_once '../model/Categoria.php';

class CategoriaController {

    public function cadastrar($dados) {
        $categoria = new Categoria();
        $categoria->nome = $dados['nome'];

        if ($categoria->cadastrar()) {
            return "Categoria cadastrada com sucesso!";
        } else {
            return "Erro ao cadastrar categoria.";
        }
    }

    public function listar() {
        $categoria = new Categoria();
        return $categoria->listar()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $categoria = new Categoria();
        return $categoria->buscarPorId($id);
    }

    public function atualizar($dados) {
        $categoria = new Categoria();
        $categoria->id = $dados['id'];
        $categoria->nome = $dados['nome'];

        if ($categoria->atualizar()) {
            return "Categoria atualizada com sucesso!";
        } else {
            return "Erro ao atualizar categoria.";
        }
    }

    public function excluir($id) {
        $categoria = new Categoria();
        if ($categoria->excluir($id)) {
            return "Categoria excluída com sucesso!";
        } else {
            return "Erro ao excluir categoria. Verifique se não existem produtos vinculados a esta categoria.";
        }
    }

    public function verificarUso($id) {

        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            $query = "SELECT COUNT(*) as total FROM produtos WHERE categoria_id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>