<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
</head>
<body>
    <h2>Cadastro de Usuário</h2>

    <!-- Formulário de cadastro -->
    <form action="" method="post">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>E-mail:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <button type="submit" name="cadastrar">Cadastrar</button>
    </form>

    <?php
        // Caminho até o controller
        $path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/';
        include($path . '/Controllers/UsuarioController.php');

        // Quando o botão "Cadastrar" for clicado...
        if (isset($_POST['cadastrar'])) {
            // Cria o objeto Usuario
            $objUsuario = new Usuario();
            $objUsuario->setNome($_POST['nome']);
            $objUsuario->setEmail($_POST['email']);
            $objUsuario->setSenha($_POST['senha']);

            // Instancia o controller
            $controller = new UsuarioController();

            // Chama o método cadastraUsuario
            $resultado = $controller->cadastraUsuario($objUsuario);

            // Exibe mensagem
            if ($resultado) {
                echo "<p>Usuário cadastrado com sucesso!</p>";
            } else {
                echo "<p>Falha ao cadastrar o usuário.</p>";
            }
        }
    ?>
</body>
</html>
