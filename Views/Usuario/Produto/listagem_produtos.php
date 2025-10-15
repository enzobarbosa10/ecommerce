<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listagem de Produtos</title>
</head>
<body>
    <h2>Listagem de Produtos</h2>

    <?php
        // Inclui o controller
        $path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/';
        include($path . '/Controllers/ProdutoController.php');

        // Instancia o controller e chama o método listarProdutos()
        $controller = new ProdutoController();
        $produtos = $controller->listarProdutos(); // ← aqui recebemos o array de produtos
    ?>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Descrição</th>
            <th>Valor</th>
            <th>Categoria</th>
            <th>Qualidade</th>
        </tr>

        <?php
            // Verifica se há produtos
            if (!empty($produtos)) {
                // foreach percorre o array $produtos
                // $produto será o apelido para cada item do array
                foreach ($produtos as $produto) {
                    echo "<tr>";
                    echo "<td>" . $produto['id'] . "</td>";
                    echo "<td>" . $produto['descricao'] . "</td>";
                    echo "<td>R$ " . number_format($produto['valor'], 2, ',', '.') . "</td>";
                    echo "<td>" . $produto['categoria'] . "</td>";
                    echo "<td>" . $produto['qualidade'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Nenhum produto cadastrado ainda.</td></tr>";
            }
        ?>
    </table>
</body>
</html>

