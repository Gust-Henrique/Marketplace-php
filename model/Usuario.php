<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $conn;
    private $table = "usuarios";

    public $id;
    public $nome;
    public $email;
    public $senha;
    public $cpf;
    public $data_nascimento;


    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }


    public function cadastrar() {
        $query = "INSERT INTO " . $this->table . " (nome, email, senha, cpf, data_nascimento) 
                  VALUES (:nome, :email, :senha, :cpf, :data_nascimento)";

        $stmt = $this->conn->prepare($query);

        $senhaHash = password_hash($this->senha, PASSWORD_DEFAULT);
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':cpf', $this->cpf);
        $stmt->bindParam(':data_nascimento', $this->data_nascimento);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

 
    public function buscarPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
public function atualizar() {
        $query = "UPDATE " . $this->table . " 
                  SET nome = :nome, email = :email, cpf = :cpf, data_nascimento = :data_nascimento 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':cpf', $this->cpf);
        $stmt->bindParam(':data_nascimento', $this->data_nascimento);
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

    public function login($email, $senha) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return false;
    }
}
?>