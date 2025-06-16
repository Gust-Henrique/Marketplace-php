<?php
require_once(__DIR__ . '/../model/Compra.php');

class CompraController {

    public function criar($produtoId, $compradorId) {
        $compra = new Compra();
        $compra->produto_id   = $produtoId;
        $compra->comprador_id = $compradorId;
        return $compra->cadastrar();
    }
}