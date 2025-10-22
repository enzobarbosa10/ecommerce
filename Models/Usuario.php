<?php
require_once __DIR__ . '/Conexao.php';
class Usuario 
{
    private $conexao;
    private $email;
    private $senha;

    public function __construct()
    {
        $this->conexao = Conexao::getInstancia();
    }

    // --- SETTERS ---
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    // --- Lógica de login ---
    public function logar()
    {
        $sql = "SELECT id, nome, email, senha 
                FROM usuarios 
                WHERE email = ? 
                LIMIT 1";

        $stmt = $this->conexao->getConexao()->prepare($sql);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($this->senha, $usuario['senha'])) {
                session_start();
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                return true;
            }
        }

        return false;
    }
}


