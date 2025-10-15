<?php 
class Conexao {
    public function getConexao() {    
        // Dados da conexão
        $host = 'localhost';
        $bd = 'ecommerce';
        $usuariobd = 'root';
        $senhadb = '';

        // Criar a conexão
        $conexao = new mysqli($host, $usuariobd, $senhadb, $bd);

        // Verifica se houve erro
        if ($conexao->connect_error) {
            die("Erro ao conectar com o banco de dados: " . $conexao->connect_error);
        }

        // Define o charset para UTF-8
        $conexao->set_charset("utf8");

        return $conexao;
    }

    public function Login() {
        $objConexao = new Conexao();
        $conexao = $objConexao->getConexao();

        $sql = "SELECT Id,Nome,Email,Senha FROM Usuario WHERE EMAIL = ' " .$this->getEmail() . "'";

        $resposta = $conexao->query($sql);
        $usuario = $resposta->fetch_assoc();
        if (!$usuario ) {
            echo "Email não cadastrado";
        } elseif ($usuario['Senha'] != $this->getSenha()) {
            echo "Senha iincorreta";
        } else {
            $this->setId($usuario['Id']);
            return true;
        }
    }    
}
?>
