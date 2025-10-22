<?php
// Configurações iniciais
$path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/';
require_once($path . 'Models/Produto.php');
require_once($path . 'Controllers/ProdutoController.php');

// Instancia o controller
$controller = new ProdutoController();

// Busca listas para os selects
$categorias = $controller->listarCategorias();
$qualidades = $controller->listarQualidades();

// Variáveis para armazenar valores do formulário
$descricao = '';
$valor = '';
$categoria = '';
$qualidade = '';
$mensagem = '';
$tipoMensagem = '';

// Processa o formulário
if (isset($_POST['cadastrar'])) {
    // Captura os dados
    $descricao = trim($_POST['descricao']);
    $valor = trim($_POST['valor']);
    $categoria = trim($_POST['categoria']);
    $qualidade = trim($_POST['qualidade']);
    
    // Cria o objeto produto
    $objProduto = new Produto();
    $objProduto->setDescricao($descricao);
    $objProduto->setValor($valor);
    $objProduto->setCategoria($categoria);
    $objProduto->setQualidade($qualidade);
    
    // Tenta cadastrar
    $resultado = $controller->cadastrarProduto($objProduto);
    
    if ($resultado['sucesso']) {
        $mensagem = $resultado['mensagem'];
        $tipoMensagem = 'sucesso';
        // Limpa os campos após sucesso
        $descricao = '';
        $valor = '';
        $categoria = '';
        $qualidade = '';
    } else {
        $mensagem = $resultado['mensagem'];
        $tipoMensagem = 'erro';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto - E-commerce</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 800px;
            width: 100%;
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
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .header p {
            margin-top: 10px;
            opacity: 0.9;
            font-size: 14px;
        }

        /* Breadcrumb */
        .breadcrumb {
            padding: 15px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: #5568d3;
        }

        .breadcrumb i {
            margin: 0 8px;
            color: #adb5bd;
            font-size: 12px;
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

        /* Formulário */
        .form-container {
            padding: 30px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-group label .obrigatorio {
            color: #dc3545;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            pointer-events: none;
        }

        .input-icon input {
            padding-left: 45px;
        }

        .form-help {
            font-size: 12px;
            color: #6c757d;
            margin-top: -5px;
        }

        /* Contador de caracteres */
        .char-counter {
            font-size: 12px;
            color: #6c757d;
            text-align: right;
        }

        /* Botões */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
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

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Preview do valor */
        .valor-preview {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            padding: 10px;
            text-align: center;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 5px;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
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

        /* Card de informação */
        .info-card {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-card i {
            color: #0066cc;
            margin-right: 10px;
        }

        .info-card p {
            margin: 0;
            font-size: 14px;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-plus-circle"></i>
                Cadastrar Novo Produto
            </h1>
            <p>Preencha os dados abaixo para adicionar um novo produto ao sistema</p>
        </div>

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="../../index.php">
                <i class="fas fa-home"></i> Início
            </a>
            <i class="fas fa-chevron-right"></i>
            <a href="listar.php">Produtos</a>
            <i class="fas fa-chevron-right"></i>
            <span>Cadastrar</span>
        </div>

        <!-- Mensagem de feedback -->
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem mensagem-<?php echo $tipoMensagem; ?>">
                <i class="fas fa-<?php echo ($tipoMensagem === 'sucesso') ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <div><?php echo $mensagem; ?></div>
            </div>
        <?php endif; ?>

        <!-- Formulário -->
        <div class="form-container">
            <div class="info-card">
                <i class="fas fa-info-circle"></i>
                <p>Todos os campos marcados com <span style="color: #dc3545;">*</span> são obrigatórios.</p>
            </div>

            <form method="POST" action="" id="formCadastro">
                <!-- Descrição -->
                <div class="form-group full-width">
                    <label for="descricao">
                        <i class="fas fa-tag"></i>
                        Descrição do Produto
                        <span class="obrigatorio">*</span>
                    </label>
                    <textarea 
                        id="descricao" 
                        name="descricao" 
                        maxlength="255" 
                        required
                        placeholder="Ex: Notebook Dell Inspiron 15 com processador Intel i7"
                    ><?php echo htmlspecialchars($descricao); ?></textarea>
                    <div class="char-counter">
                        <span id="charCount">0</span>/255 caracteres
                    </div>
                    <div class="form-help">
                        Seja descritivo e inclua informações importantes sobre o produto
                    </div>
                </div>

                <div class="form-row">
                    <!-- Valor -->
                    <div class="form-group">
                        <label for="valor">
                            <i class="fas fa-dollar-sign"></i>
                            Valor (R$)
                            <span class="obrigatorio">*</span>
                        </label>
                        <div class="input-icon">
                            <i class="fas fa-dollar-sign"></i>
                            <input 
                                type="number" 
                                id="valor" 
                                name="valor" 
                                step="0.01" 
                                min="0" 
                                max="999999.99"
                                required
                                placeholder="0,00"
                                value="<?php echo htmlspecialchars($valor); ?>"
                            >
                        </div>
                        <div id="valorPreview" class="valor-preview" style="display: none;">
                            R$ 0,00
                        </div>
                    </div>

                    <!-- Categoria -->
                    <div class="form-group">
                        <label for="categoria">
                            <i class="fas fa-folder"></i>
                            Categoria
                            <span class="obrigatorio">*</span>
                        </label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo ($categoria === $cat) ? 'selected' : ''; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Qualidade -->
                    <div class="form-group">
                        <label for="qualidade">
                            <i class="fas fa-star"></i>
                            Qualidade/Estado
                            <span class="obrigatorio">*</span>
                        </label>
                        <select id="qualidade" name="qualidade" required>
                            <option value="">Selecione a qualidade</option>
                            <?php foreach ($qualidades as $qual): ?>
                                <option value="<?php echo $qual; ?>" <?php echo ($qualidade === $qual) ? 'selected' : ''; ?>>
                                    <?php echo $qual; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-help">
                            Indique o estado atual do produto
                        </div>
                    </div>

                    <!-- Espaço para futuras expansões (ex: estoque) -->
                    <div class="form-group">
                        <label>
                            <i class="fas fa-info-circle"></i>
                            Dica
                        </label>
                        <div style="padding: 10px; background: #f8f9fa; border-radius: 8px; font-size: 13px; color: #6c757d;">
                            <strong>Produtos novos</strong> tendem a ter melhor aceitação no mercado
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="form-actions">
                    <a href="listar.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                    <button type="submit" name="cadastrar" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-save"></i>
                        Cadastrar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Contador de caracteres
        const descricaoInput = document.getElementById('descricao');
        const charCount = document.getElementById('charCount');

        descricaoInput.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            
            // Muda a cor quando chegar perto do limite
            if (this.value.length > 200) {
                charCount.style.color = '#dc3545';
            } else {
                charCount.style.color = '#6c757d';
            }
        });

        // Atualiza o contador no carregamento
        charCount.textContent = descricaoInput.value.length;

        // Preview do valor
        const valorInput = document.getElementById('valor');
        const valorPreview = document.getElementById('valorPreview');

        valorInput.addEventListener('input', function() {
            if (this.value) {
                const valor = parseFloat(this.value);
                valorPreview.style.display = 'block';
                valorPreview.textContent = 'R$ ' + valor.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                valorPreview.style.display = 'none';
            }
        });

        // Atualiza o preview no carregamento
        if (valorInput.value) {
            valorInput.dispatchEvent(new Event('input'));
        }

        // Loading no submit
        const form = document.getElementById('formCadastro');
        const btnSubmit = document.getElementById('btnSubmit');

        form.addEventListener('submit', function(e) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner spinner"></i> Cadastrando...';
        });

        // Validação adicional antes do submit
        form.addEventListener('submit', function(e) {
            const descricao = descricaoInput.value.trim();
            const valor = parseFloat(valorInput.value);
            const categoria = document.getElementById('categoria').value;
            const qualidade = document.getElementById('qualidade').value;

            // Validação de descrição
            if (descricao.length < 3) {
                e.preventDefault();
                alert('A descrição deve ter pelo menos 3 caracteres.');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fas fa-save"></i> Cadastrar Produto';
                return;
            }

            // Validação de valor
            if (isNaN(valor) || valor <= 0) {
                e.preventDefault();
                alert('Por favor, informe um valor válido maior que zero.');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fas fa-save"></i> Cadastrar Produto';
                return;
            }

            // Validação de categoria
            if (!categoria) {
                e.preventDefault();
                alert('Por favor, selecione uma categoria.');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fas fa-save"></i> Cadastrar Produto';
                return;
            }

            // Validação de qualidade
            if (!qualidade) {
                e.preventDefault();
                alert('Por favor, selecione a qualidade do produto.');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fas fa-save"></i> Cadastrar Produto';
                return;
            }
        });

        // Auto-hide mensagem após 5 segundos
        const mensagem = document.querySelector('.mensagem');
        if (mensagem) {
            setTimeout(function() {
                mensagem.style.opacity = '0';
                setTimeout(function() {
                    mensagem.style.display = 'none';
                }, 300);
            }, 5000);
        }

        // Formata o valor ao sair do campo
        valorInput.addEventListener('blur', function() {
            if (this.value) {
                const valor = parseFloat(this.value);
                this.value = valor.toFixed(2);
            }
        });
    </script>
</body>
</html>