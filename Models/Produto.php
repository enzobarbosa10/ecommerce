<?php
class Produto {
    private $id;
    private $descricao;
    private $valor;
    private $categoria;
    private $qualidade;

    // Getters e Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }

    public function getValor() { return $this->valor; }
    public function setValor($valor) { $this->valor = $valor; }

    public function getCategoria() { return $this->categoria; }
    public function setCategoria($categoria) { $this->categoria = $categoria; }

    public function getQualidade() { return $this->qualidade; }
    public function setQualidade($qualidade) { $this->qualidade = $qualidade; }

    //  Método de cadastro
    public function cadastrar() {
        include($_SERVER['DOCUMENT_ROOT'] . '/ecommerce/connection.php');

        $sql = "INSERT INTO produtos (descricao, valor, categoria, qualidade) 
                VALUES ('$this->descricao', '$this->valor', '$this->categoria', '$this->qualidade')";
        
        if ($conn->query($sql) === TRUE) {
            return true;
        } else {
            echo "Erro ao cadastrar produto: " . $conn->error;
            return false;
        }
    }

    // 🧾 Método para listar todos os produtos
    public function listar() {
        include($_SERVER['DOCUMENT_ROOT'] . '/ecommerce/connection.php');

        $sql = "SELECT * FROM produtos"; // Seleciona todas as colunas
        $resultado = mysqli_query($conn, $sql);

        $arrayProdutos = [];

        // Percorre cada linha retornada e adiciona ao array
        while ($produto = mysqli_fetch_assoc($resultado)) {
            $arrayProdutos[] = $produto;
        }

        return $arrayProdutos;
    }
}
?>

