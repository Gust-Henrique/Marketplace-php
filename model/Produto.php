<?php
require_once '../config/database.php';

class Produto {
    private $conn;
    private $table = "produtos";

    public $id;
    public $nome;
    public $descricao;
    public $preco;
    public $imagem;
    public $categoria_id;
    public $usuario_id;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function cadastrar() {
        $query = "INSERT INTO " . $this->table . " 
                  (nome, descricao, preco, imagem, categoria_id, usuario_id) 
                  VALUES (:nome, :descricao, :preco, :imagem, :categoria_id, :usuario_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':preco', $this->preco);
        $stmt->bindParam(':imagem', $this->imagem);
        $stmt->bindParam(':categoria_id', $this->categoria_id);
        $stmt->bindParam(':usuario_id', $this->usuario_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    public function listar() {

        $query = "SELECT p.*, c.nome AS categoria_nome, u.nome AS usuario_nome 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  LEFT JOIN usuarios u ON p.usuario_id = u.id
                  ORDER BY p.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function buscarPorId($id) {

        $query = "SELECT p.*, c.nome AS categoria_nome, u.nome AS usuario_nome 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  LEFT JOIN usuarios u ON p.usuario_id = u.id
                  WHERE p.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar() {
        $query = "UPDATE " . $this->table . " 
                  SET nome = :nome, descricao = :descricao, preco = :preco, 
                      imagem = :imagem, categoria_id = :categoria_id 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':preco', $this->preco);
        $stmt->bindParam(':imagem', $this->imagem);
        $stmt->bindParam(':categoria_id', $this->categoria_id);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function excluir($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function buscarPorCategoria($categoria_id) {
        $query = "SELECT p.*, c.nome AS categoria_nome, u.nome AS usuario_nome 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  LEFT JOIN usuarios u ON p.usuario_id = u.id
                  WHERE p.categoria_id = :categoria_id
                  ORDER BY p.id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':categoria_id', $categoria_id);
        $stmt->execute();
        return $stmt;
    }

    public function buscarPorUsuario($usuario_id) {
        $query = "SELECT p.*, c.nome AS categoria_nome 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.usuario_id = :usuario_id
                  ORDER BY p.id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        return $stmt;
    }
}
?>