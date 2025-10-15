<?php
// Inicia a sessão e importa o controller
session_start();
include_once('../../Controllers/UsuarioController.php');

// Instancia o controller
$usuarioController = new UsuarioController();

// Pega o ID do usuário logado da sessão
$id = $_SESSION['usuario_id'];

// Busca os dados do usuário no banco
$usuario = $usuarioController->getUsuario($id);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
</head>
<body>
    <h2>Editar Usuário</h2>

    <form action="" method="post">
        <label>Nome:</label>
        <!-- Recebe o valor do banco -->
        <input type="text" name="nome" value="<?php echo $usuario['nome']; ?>" required><br><br>

        <label>E-mail:</label>
        <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required><br><br>

        <label>Senha:</label>
        <input type="password" name="senha" value="<?php echo $usuario['senha']; ?>" required><br><br>

        <button type="submit" name="editar">Salvar Alterações</button>
    </form>

    <?php
    // Quando o botão for pressionado
    if (isset($_POST['editar'])) {
        $novoUsuario = new Usuario();
        $novoUsuario->setId($id);
        $novoUsuario->setNome($_POST['nome']);
        $novoUsuario->setEmail($_POST['email']);
        $novoUsuario->setSenha($_POST['senha']);

        if ($usuarioController->editarUsuario($novoUsuario)) {
            echo "<p>Usuário atualizado com sucesso!</p>";
        } else {
            echo "<p>Erro ao atualizar usuário.</p>";
        }
    }
    ?>
</body>
</html>
