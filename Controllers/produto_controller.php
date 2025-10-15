<?php
$path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/';
include($path . '/Models/Produto.php');

class ProdutoController {

    // Cadastrar produto
    public function cadastrarProduto($objProduto) {
        if (empty($objProduto->getDescricao())) {
            echo "A descrição é obrigatória.<br>";
            return false;
        }

        if (empty($objProduto->getValor()) || !is_numeric($objProduto->getValor())) {
            echo "Informe um valor válido.<br>";
            return false;
        }

        if (empty($objProduto->getCategoria())) {
            echo "A categoria é obrigatória.<br>";
            return false;
        }

        if (empty($objProduto->getQualidade())) {
            echo "A qualidade é obrigatória.<br>";
            return false;
        }

        return $objProduto->cadastrar();
    }

    // Listar produtos
    public function listarProdutos() {
        $objProduto = new Produto();
        return $objProduto->listar();
    }
}
?>
