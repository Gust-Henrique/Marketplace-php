<?php
require_once(DIR . '/../config/database.php');

class Compra {
    private $conn;
    private $table = "compras";

    public $id;
    public $produto_id;
    public $comprador_id;
    public $data_compra;
    public $status;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function cadastrar() {
        $sql = "INSERT INTO {$this->table}
                (produto_id, comprador_id, data_compra, status)
                VALUES (:produto_id, :comprador_id, NOW(), :status)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':produto_id',  $this->produto_id,  PDO::PARAM_INT);
        $stmt->bindParam(':comprador_id',$this->comprador_id,PDO::PARAM_INT);
        $status = $this->status ?? 'pendente';
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }
}
