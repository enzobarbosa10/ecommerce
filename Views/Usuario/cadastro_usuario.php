<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        .mensagem {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .link-login {
            text-align: center;
            margin-top: 20px;
        }
        
        .link-login a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        
        .link-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Usuário</h2>
        
        <?php
        // Exibe mensagem se houver
        if (isset($mensagem)) {
            $classe = (strpos($mensagem, 'sucesso') !== false) ? 'sucesso' : 'erro';
            echo "<div class='mensagem $classe'>$mensagem</div>";
        }
        ?>
        
        <form action="" method="post">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required>
            </div>
            
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha (mínimo 6 caracteres)" required>
            </div>
            
            <button type="submit" name="cadastrar">Cadastrar</button>
        </form>
        
        <div class="link-login">
            <p>Já tem uma conta? <a href="../../index.php">Fazer login</a></p>
        </div>
    </div>
</body>
</html>

<?php
// Importa as classes necessárias
require_once '../../Model/Usuario.php';
require_once '../../Controller/UsuarioController.php';

// Verifica se o botão cadastrar foi pressionado
if (isset($_POST['cadastrar'])) {
    // Captura os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    // Cria o objeto usuário
    $objUsuario = new UsuarioController();
    $objUsuario->setNome($nome);
    $objUsuario->setEmail($email);
    $objUsuario->setSenha($senha);
    
    // Instancia o controller
    $usuarioController = new UsuarioController();
    
    // Chama o método de cadastro
    $mensagem = $usuarioController->cadastraUsuario($objUsuario);
    
    // Exibe a mensagem (recarrega a página com a mensagem)
    echo "<script>window.location.href = '?msg=" . urlencode($mensagem) . "';</script>";
}

// Exibe mensagem se vier da URL
if (isset($_GET['msg'])) {
    $mensagem = $_GET['msg'];
}
?>