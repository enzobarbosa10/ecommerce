<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Usuários</title>
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
            font-size: 28px;
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
        
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
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
        
        .link-cadastro {
            text-align: center;
            margin-top: 20px;
        }
        
        .link-cadastro a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        
        .link-cadastro a:hover {
            text-decoration: underline;
        }
        
        .esqueci-senha {
            text-align: right;
            margin-top: 10px;
        }
        
        .esqueci-senha a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .esqueci-senha a:hover {
            text-decoration: underline;
        }
        
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background-color: #ddd;
        }
        
        .divider span {
            background-color: white;
            padding: 0 10px;
            position: relative;
            color: #999;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        
        <?php
        // Inicia sessão mais cedo para checagens/redirects
        session_start();

        // Exibe mensagem se houver
        if (isset($mensagem)) {
            $classe = (strpos(strtolower($mensagem), 'sucesso') !== false) ? 'sucesso' : 'erro';
            echo "<div class='mensagem $classe'>$mensagem</div>";
        }
        ?>
        
        <form action="" method="post">
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
            </div>
            
            <div class="esqueci-senha">
                <a href="#">Esqueci minha senha</a>
            </div>
            
            <button type="submit" name="login">Entrar</button>
        </form>
        
        <div class="divider">
            <span>OU</span>
        </div>
        
        <div class="link-cadastro">
            <p>Não tem uma conta? <a href="Views/Usuario/cadastro_usuario.php">Cadastre-se</a></p>
        </div>
    </div>
</body>
</html>

<?php
// Importa as classes necessárias
require_once 'Models/Usuario.php';
require_once 'Controller/UsuarioController.php';

// Verifica se o botão login foi pressionado
if (isset($_POST['login'])) {
    // Captura os dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    // Instancia o controller
    $usuarioController = new UsuarioController();
    
    // Chama o método de login
    $mensagem = $usuarioController->login($email, $senha);
    
    // Verifica se o login foi bem-sucedido
    if (strpos(strtolower($mensagem), 'sucesso') !== false) {
        // Redireciona para a página principal do sistema
        header('Location: Views/dashboard.php');
        exit();
    }
    
    // Se houver erro, a mensagem será exibida no topo do formulário
}

// Exibe mensagem se vier da URL (útil para redirecionamentos)
if (isset($_GET['msg'])) {
    $mensagem = urldecode($_GET['msg']);
}

// Verifica se o usuário já está logado
session_start();
if (isset($_SESSION['usuario_id'])) {
    // Redireciona para o dashboard se já estiver logado
    header('Location: Views/dashboard.php');
    exit();
}
?>