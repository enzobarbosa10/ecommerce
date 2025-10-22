<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?msg=' . urlencode('Você precisa estar logado para acessar esta página.'));
    exit();
}

// Importa as classes necessárias
$path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/';
require_once($path . 'Models/Usuario.php');
require_once($path . 'Controllers/UsuarioController.php');

// Instancia o controller
$usuarioController = new UsuarioController();

// Pega o ID do usuário logado
$id = $_SESSION['usuario_id'];

// Busca os dados atuais do usuário
$usuario = $usuarioController->buscarUsuarioPorId($id);

// Se não encontrou o usuário, redireciona
if (!$usuario) {
    header('Location: ../../index.php?msg=' . urlencode('Usuário não encontrado.'));
    exit();
}

// Variáveis para armazenar valores do formulário
$nome = $usuario['nome'];
$email = $usuario['email'];
$mensagem = '';
$tipoMensagem = '';

// Processa o formulário de edição
if (isset($_POST['editar'])) {
    // Captura os dados
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senhaAtual = trim($_POST['senha_atual']);
    $novaSenha = trim($_POST['nova_senha']);
    $confirmarSenha = trim($_POST['confirmar_senha']);
    
    // Valida se a senha atual foi informada
    if (empty($senhaAtual)) {
        $mensagem = 'Por favor, informe sua senha atual para confirmar as alterações.';
        $tipoMensagem = 'erro';
    } else {
        // Verifica se a senha atual está correta
        if ($usuarioController->verificarSenha($id, $senhaAtual)) {
            
            // Cria o objeto usuário
            $objUsuario = new Usuario();
            $objUsuario->setId($id);
            $objUsuario->setNome($nome);
            $objUsuario->setEmail($email);
            
            // Verifica se quer alterar a senha
            if (!empty($novaSenha)) {
                // Valida se as senhas coincidem
                if ($novaSenha !== $confirmarSenha) {
                    $mensagem = 'A nova senha e a confirmação não coincidem.';
                    $tipoMensagem = 'erro';
                } elseif (strlen($novaSenha) < 6) {
                    $mensagem = 'A nova senha deve ter pelo menos 6 caracteres.';
                    $tipoMensagem = 'erro';
                } else {
                    // Define a nova senha
                    $objUsuario->setSenha($novaSenha);
                }
            }
            
            // Se não houver erro, tenta atualizar
            if (empty($mensagem)) {
                $resultado = $usuarioController->editarUsuario($objUsuario);
                
                if ($resultado['sucesso']) {
                    $mensagem = $resultado['mensagem'];
                    $tipoMensagem = 'sucesso';
                    
                    // Atualiza os dados da sessão
                    $_SESSION['usuario_nome'] = $nome;
                    $_SESSION['usuario_email'] = $email;
                    
                    // Atualiza as variáveis
                    $usuario['nome'] = $nome;
                    $usuario['email'] = $email;
                } else {
                    $mensagem = $resultado['mensagem'];
                    $tipoMensagem = 'erro';
                }
            }
        } else {
            $mensagem = 'Senha atual incorreta. Verifique e tente novamente.';
            $tipoMensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - E-commerce</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-left h1 {
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .header-left p {
            opacity: 0.9;
            font-size: 14px;
        }

        .header-right {
            display: flex;
            gap: 10px;
        }

        .btn-header {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-header:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Mensagens */
        .mensagem {
            padding: 15px 20px;
            margin: 20px 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .mensagem-sucesso {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensagem-erro {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .mensagem i {
            font-size: 20px;
        }

        /* Layout de duas colunas */
        .content {
            display: grid;
            grid-template-columns: 300px 1fr;
            min-height: 500px;
        }

        /* Sidebar do perfil */
        .profile-sidebar {
            background: #f8f9fa;
            padding: 30px 20px;
            border-right: 1px solid #e9ecef;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 48px;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .profile-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-info h3 {
            color: #495057;
            margin-bottom: 5px;
            font-size: 18px;
        }

        .profile-info p {
            color: #6c757d;
            font-size: 14px;
        }

        .profile-stats {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: #6c757d;
            font-size: 13px;
        }

        .stat-value {
            color: #495057;
            font-weight: 600;
            font-size: 13px;
        }

        /* Formulário */
        .form-container {
            padding: 30px;
        }

        .section-title {
            font-size: 20px;
            color: #495057;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-group label .obrigatorio {
            color: #dc3545;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            pointer-events: none;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-help {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #495057;
        }

        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-box i {
            color: #856404;
            margin-right: 10px;
        }

        .info-box p {
            margin: 0;
            font-size: 13px;
            color: #856404;
        }

        .alert-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-box i {
            color: #721c24;
            margin-right: 10px;
        }

        .alert-box p {
            margin: 0;
            font-size: 13px;
            color: #721c24;
        }

        /* Botões */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Password strength meter */
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 5px;
            transition: all 0.3s;
        }

        .strength-weak {
            background: #dc3545;
            width: 33%;
        }

        .strength-medium {
            background: #ffc107;
            width: 66%;
        }

        .strength-strong {
            background: #28a745;
            width: 100%;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .content {
                grid-template-columns: 1fr;
            }

            .profile-sidebar {
                border-right: none;
                border-bottom: 1px solid #e9ecef;
            }

            .header {
                flex-direction: column;
                text-align: center;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Loading spinner */
        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>
                    <i class="fas fa-user-edit"></i>
                    Editar Perfil
                </h1>
                <p>Gerencie suas informações pessoais</p>
            </div>
            <div class="header-right">
                <a href="../dashboard.php" class="btn-header">
                    <i class="fas fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </div>

        <!-- Mensagem de feedback -->
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem mensagem-<?php echo $tipoMensagem; ?>">
                <i class="fas fa-<?php echo ($tipoMensagem === 'sucesso') ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <div><?php echo $mensagem; ?></div>
            </div>
        <?php endif; ?>

        <!-- Conteúdo -->
        <div class="content">
            <!-- Sidebar do perfil -->
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($usuario['nome'], 0, 1)); ?>
                </div>
                
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($usuario['nome']); ?></h3>
                    <p><?php echo htmlspecialchars($usuario['email']); ?></p>
                </div>

                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-label">
                            <i class="fas fa-calendar"></i> Cadastro
                        </span>
                        <span class="stat-value">
                            <?php 
                            if (isset($usuario['data_cadastro'])) {
                                echo date('d/m/Y', strtotime($usuario['data_cadastro']));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">
                            <i class="fas fa-clock"></i> Última Atualização
                        </span>
                        <span class="stat-value">
                            <?php 
                            if (isset($usuario['data_atualizacao'])) {
                                echo date('d/m/Y', strtotime($usuario['data_atualizacao']));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">
                            <i class="fas fa-shield-alt"></i> Status
                        </span>
                        <span class="stat-value" style="color: #28a745;">
                            <i class="fas fa-check-circle"></i> Ativo
                        </span>
                    </div>
                </div>
            </div>

            <!-- Formulário -->
            <div class="form-container">
                <form method="POST" action="" id="formEditar">
                    <!-- Seção: Informações Pessoais -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-user"></i>
                            Informações Pessoais
                        </h2>

                        <div class="form-group">
                            <label for="nome">
                                <i class="fas fa-user"></i>
                                Nome Completo
                                <span class="obrigatorio">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input 
                                    type="text" 
                                    id="nome" 
                                    name="nome" 
                                    value="<?php echo htmlspecialchars($usuario['nome']); ?>"
                                    required
                                    minlength="3"
                                >
                            </div>
                            <div class="form-help">Informe seu nome completo</div>
                        </div>

                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i>
                                E-mail
                                <span class="obrigatorio">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="<?php echo htmlspecialchars($usuario['email']); ?>"
                                    required
                                >
                            </div>
                            <div class="form-help">Seu e-mail é usado para login</div>
                        </div>
                    </div>

                    <!-- Seção: Segurança -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-lock"></i>
                            Segurança
                        </h2>

                        <div class="alert-box">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p><strong>Atenção:</strong> Para confirmar qualquer alteração, você precisa informar sua senha atual.</p>
                        </div>

                        <div class="form-group">
                            <label for="senha_atual">
                                <i class="fas fa-key"></i>
                                Senha Atual
                                <span class="obrigatorio">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input 
                                    type="password" 
                                    id="senha_atual" 
                                    name="senha_atual" 
                                    required
                                    placeholder="Digite sua senha atual"
                                >
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('senha_atual')"></i>
                            </div>
                            <div class="form-help">Necessário para confirmar as alterações</div>
                        </div>

                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <p><strong>Alterar senha:</strong> Preencha os campos abaixo apenas se desejar alterar sua senha.</p>
                        </div>

                        <div class="form-group">
                            <label for="nova_senha">
                                <i class="fas fa-lock"></i>
                                Nova Senha (opcional)
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input 
                                    type="password" 
                                    id="nova_senha" 
                                    name="nova_senha" 
                                    minlength="6"
                                    placeholder="Deixe em branco para manter a atual"
                                >
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('nova_senha')"></i>
                            </div>
                            <div id="passwordStrength" class="password-strength"></div>
                            <div class="form-help">Mínimo de 6 caracteres</div>
                        </div>

                        <div class="form-group">
                            <label for="confirmar_senha">
                                <i class="fas fa-check-circle"></i>
                                Confirmar Nova Senha
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-check-circle input-icon"></i>
                                <input 
                                    type="password" 
                                    id="confirmar_senha" 
                                    name="confirmar_senha" 
                                    placeholder="Confirme a nova senha"
                                >
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirmar_senha')"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="form-actions">
                        <a href="../dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                        <button type="submit" name="editar" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-save"></i>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle mostrar/ocultar senha
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Medidor de força da senha
        const novaSenhaInput = document.getElementById('nova_senha');
        const passwordStrength = document.getElementById('passwordStrength');

        novaSenhaInput.addEventListener('input', function() {
            const senha = this.value;
            
            if (senha.length === 0) {
                passwordStrength.className = 'password-strength';
                return;
            }
            
            let forca = 0;
            
            // Critérios de força
            if (senha.length >= 6) forca++;
            if (senha.length >= 10) forca++;
            if (/[a-z]/.test(senha) && /[A-Z]/.test(senha)) forca++;
            if (/[0-9]/.test(senha)) forca++;
            if (/[^a-zA-Z0-9]/.test(senha)) forca++;
            
            if (forca <= 2) {
                passwordStrength.className = 'password-strength strength-weak';
            } else if (forca <= 3) {
                passwordStrength.className = 'password-strength strength-medium';
            } else {
                passwordStrength.className = 'password-strength strength-strong';
            }
        });

        // Validação do formulário
        const form = document.getElementById('formEditar');
        const btnSubmit = document.getElementById('btnSubmit');

        form.addEventListener('submit', function(e) {
            const novaSenha = novaSenhaInput.value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            
            // Se preencheu nova senha, verifica confirmação
            if (novaSenha && novaSenha !== confirmarSenha) {
                e.preventDefault();
                alert('A nova senha e a confirmação não coincidem!');
                return;
            }
            
            // Se preencheu nova senha, valida tamanho
            if (novaSenha && novaSenha.length < 6) {
                e.preventDefault();
                alert('A nova senha deve ter pelo menos 6 caracteres!');
                return;
            }
            
            // Loading
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner spinner"></i> Salvando...';
        });

        // Auto-hide mensagem
        const mensagem = document.querySelector('.mensagem');
        if (mensagem) {
            setTimeout(function() {
                mensagem.style.opacity = '0';
                setTimeout(function() {
                    mensagem.style.display = 'none';
                }, 300);
            }, 5000);
        }
    </script>
</body>
</html>