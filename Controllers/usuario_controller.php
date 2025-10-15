 <?php
$path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/';
include($path . '/Models/Usuario.php');

class UsuarioController {

    public function login($objUsuario) {
        session_start(); // ✅ Sempre iniciar a sessão antes de usar $_SESSION

        // Exemplo de login simples (simulação)
        if ($objUsuario->getEmail() == "teste@teste.com" && $objUsuario->getSenha() == "123456") {
            // Aqui normalmente você buscaria o usuário no banco de dados.
            // Simulando o ID retornado do banco:
            $usuario_id = 1;

            // Armazena o ID do usuário logado na sessão
            $_SESSION['usuario_id'] = $usuario_id;

            echo "Login realizado com sucesso!<br>";
            echo "Sessão criada para o usuário ID: " . $_SESSION['usuario_id'];
        } else {
            echo "E-mail ou senha incorretos!";
        }
    }
    public function editarUsuario($objUsuario) {
    // Aqui validamos e chamamos o método de atualização do Model
    if (empty($objUsuario->getNome()) || empty($objUsuario->getEmail())) {
        echo "Nome e e-mail são obrigatórios.";
        return false;
    }

    // Chama o método que atualiza no banco
    if ($objUsuario->editar()) {
        echo "<p>Dados atualizados com sucesso!</p>";
    } else {
        echo "<p>Erro ao atualizar usuário.</p>";
    }
    }

}
?>
