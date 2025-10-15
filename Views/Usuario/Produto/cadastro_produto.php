<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produto</title>
</head>
<body>
    <h2>Cadastro de Produto</h2>

    <form action="" method="post">
        <label>Descrição:</label><br>
        <input type="text" name="descricao" required><br><br>

        <label>Valor:</label><br>
        <input type="number" step="0.01" name="valor" required><br><br>

        <label>Categoria:</label><br>
        <input type="text" name="categoria" required><br><br>

        <label>Qualidade:</label><br>
        <input type="text" name="qualidade" required><br><br>

        <button type="submit" name="cadastrar">Cadastrar</button>
    </form>

    <?php
        $path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/';
        include($path . '/Controllers/ProdutoController.php');

        if (isset($_POST['cadastrar'])) {
            $objProduto = new Produto();
            $objProduto->setDescricao($_POST['descricao']);
            $objProduto->setValor($_POST['valor']);
            $objProduto->setCategoria($_POST['categoria']);
            $objProduto->setQualidade($_POST['qualidade']);

            $controller = new ProdutoController();
            $resultado = $controller->cadastrarProduto($objProduto);

            if ($resultado) {
                echo "<p>Produto cadastrado com sucesso!</p>";
            }
        }
    ?>
</body>
</html>
