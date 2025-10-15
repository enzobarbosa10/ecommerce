<?php
include_once('Conexao.php');

class Usuario {
    private $id;
    private $nome;
    private $email;
    private $senha;

    // Getters e Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getSenha() { return $this->senha; }
    public function setSenha($senha) { $this->senha = $senha; }

    // Método de cadastro
    public function cadastrar() {
        $con = new Conexao();
        $sql = "INSERT INTO usuarios (nome, email, senha)
                VALUES ('{$this->nome}', '{$this->email}', '{$this->senha}')";
        return $con->conectar()->query($sql);
    }

    // 🔧 Método de edição (NOVO)
    public function editar() {
        $con = new Conexao();
        $sql = "UPDATE usuarios 
                SET nome = '{$this->nome}', 
                    email = '{$this->email}', 
                    senha = '{$this->senha}'
                WHERE id = {$this->id}";
        return $con->conectar()->query($sql);
    }

    // Método para buscar usuário por ID (usado na edição)
    public function buscarPorId($id) {
        $con = new Conexao();
        $sql = "SELECT * FROM usuarios WHERE id = $id";
        $resultado = $con->conectar()->query($sql);
        return $resultado->fetch_assoc();
    }
}
?>
