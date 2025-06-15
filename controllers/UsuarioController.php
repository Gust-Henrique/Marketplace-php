<?php
require_once 'model/Usuario.php';

class UsuarioController {

    public function cadastrar($dados) {
        $usuario = new Usuario();
        $usuario->nome = $dados['nome'];
        $usuario->email = $dados['email'];
        $usuario->senha = $dados['senha'];
        $usuario->cpf = $dados['cpf'];
        $usuario->data_nascimento = $dados['data_nascimento'];

        if ($usuario->cadastrar()) {
            return "Usuário cadastrado com sucesso!";
        } else {
            return "Erro ao cadastrar usuário.";
        }
    }

    public function listar() {
        $usuario = new Usuario();
        return $usuario->listar()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $usuario = new Usuario();
        return $usuario->buscarPorId($id);
    }

    public function atualizar($dados) {
        $usuario = new Usuario();
        $usuario->id = $dados['id'];
        $usuario->nome = $dados['nome'];
        $usuario->email = $dados['email'];
        $usuario->cpf = $dados['cpf'];
        $usuario->data_nascimento = $dados['data_nascimento'];

        if ($usuario->atualizar()) {
            return "Usuário atualizado com sucesso!";
        } else {
            return "Erro ao atualizar usuário.";
        }
    }

    public function excluir($id) {
        $usuario = new Usuario();
        if ($usuario->excluir($id)) {
            return "Usuário excluído com sucesso!";
        } else {
            return "Erro ao excluir usuário.";
        }
    }

    public function login($email, $senha) {
        $usuario = new Usuario();
        $resultado = $usuario->login($email, $senha);

        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
}
?>
