<?php
// model/Usuario.php

class Usuario
{
    private $id;
    private $nome;
    private $email;
    private $senha;
    private $conexao;

    /**
     * Construtor - Inicializa a conexão com o banco de dados
     */
    public function __construct()
    {
        // Aqui você deve incluir sua classe de conexão
        // require_once 'Conexao.php';
        // $this->conexao = Conexao::getConexao();
    }

    // Getters e Setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    /**
     * Método para realizar o login do usuário
     */
    public function logar()
    {
        try {
            // Prepara a query SQL
            $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();

            // Verifica se encontrou o usuário
            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verifica a senha (considerando que está usando password_hash)
                if (password_verify($this->senha, $usuario['senha'])) {
                    // Inicia a sessão e armazena os dados do usuário
                    session_start();
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email'] = $usuario['email'];

                    return true;
                }
            }

            return false;
        } catch (PDOException $e) {
            error_log("Erro no login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Método para cadastrar um novo usuário
     */
    public function Cadastrar()
    {
        try {
            // Verifica se o e-mail já está cadastrado
            $sqlVerifica = "SELECT id FROM usuarios WHERE email = :email";
            $stmtVerifica = $this->conexao->prepare($sqlVerifica);
            $stmtVerifica->bindParam(':email', $this->email);
            $stmtVerifica->execute();

            if ($stmtVerifica->rowCount() > 0) {
                return false; // E-mail já cadastrado
            }

            // Criptografa a senha
            $senhaHash = password_hash($this->senha, PASSWORD_DEFAULT);

            // Prepara a query SQL para inserção
            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':nome', $this->nome);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':senha', $senhaHash);

            // Executa a inserção
            if ($stmt->execute()) {
                return true;
            }

            return false;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Método para buscar todos os usuários
     */
    public function listarTodos()
    {
        try {
            $sql = "SELECT id, nome, email FROM usuarios ORDER BY nome";
            $stmt = $this->conexao->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Método para buscar um usuário por ID
     */
    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT id, nome, email FROM usuarios WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário: " . $e->getMessage());
            return null;
        }
    }
}

