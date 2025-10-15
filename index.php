<html>
<body>  
    <form action="" method="post">
     Email: <input type="text" name="email" value="" required><br>
     Senha: <input type="password" name="senha" value="" required><br>
     <button type="submit" name="logar">Enviar</button>
     <button type="button" onclick="window.location.href='cadastro.php'">Cadastrar</button>
    </form>
</body>
</html>

<?php 
    $path = $_SERVER['DOCUMENT_ROOT']. '/ecommerce/';
    include($path . '/Models/Usuario.php');
    include($path . '/Controllers/usuario_controller.php');

    // Verifica se houve envio
    if(isset($_POST['logar'])) {
        $objUsuario = new Usuario();
        $objUsuario->setEmail( $_POST[ 'email' ] );
        $objUsuario->setSenha( $_POST[ 'senha' ] );

        $controllerUsuario = new UsuarioController();

        $resposta = $controllerUsuario->validaUsuario($objUsuario);

        if($resposta == "Sucesso") {

            if($resposta == "Sucesso") {
                session_start();
                $_SESSION['usuario_id'] = $objUsuario->getId();
                header("Location: http://localhost/ecommerce/Views/Produto/listagem_produtos.php");
            } else {
                echo "Acesso negado!<br>";
            }
           
        }   




        echo $resposta;           
    }    
?>
