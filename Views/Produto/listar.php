<?php
// Configurações iniciais
$path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/';
require_once($path . 'Controllers/ProdutoController.php');

// Instancia o controller
$controller = new ProdutoController();

// Paginação
$itensPorPagina = 10;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $itensPorPagina;

// Filtros
$categoriaFiltro = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$qualidadeFiltro = isset($_GET['qualidade']) ? $_GET['qualidade'] : '';
$buscaTermo = isset($_GET['busca']) ? $_GET['busca'] : '';

// Busca produtos com filtros
if (!empty($buscaTermo)) {
    $produtos = $controller->pesquisarProdutos($buscaTermo);
} elseif (!empty($categoriaFiltro)) {
    $produtos = $controller->buscarPorCategoria($categoriaFiltro);
} elseif (!empty($qualidadeFiltro)) {
    $produtos = $controller->buscarPorQualidade($qualidadeFiltro);
} else {
    $produtos = $controller->listarProdutos();
}

// Calcula total de páginas
$totalProdutos = count($produtos);
$totalPaginas = ceil($totalProdutos / $itensPorPagina);

// Aplica paginação no array
$produtosPaginados = array_slice($produtos, $offset, $itensPorPagina);

// Listas para filtros
$categorias = $controller->listarCategorias();
$qualidades = $controller->listarQualidades();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Produtos - E-commerce</title>
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
            max-width: 1400px;
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

        .header h1 {
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-novo {
            background: white;
            color: #667eea;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-novo:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Filtros e Busca */
        .filters-section {
            padding: 25px 30px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .filters-container {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }

        .filter-group input,
        .filter-group select {
            padding: 10px 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-filtrar {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-filtrar:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-limpar {
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-limpar:hover {
            background: #5a6268;
        }

        /* Estatísticas */
        .stats {
            padding: 20px 30px;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
        }

        .stats-info {
            color: #6c757d;
            font-size: 14px;
        }

        .stats-info strong {
            color: #495057;
            font-size: 16px;
        }

        /* Tabela */
        .table-container {
            padding: 30px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: #f8f9fa;
        }

        thead th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s;
        }

        tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }

        tbody td {
            padding: 15px;
            color: #495057;
        }

        .produto-descricao {
            font-weight: 600;
            color: #212529;
        }

        .produto-valor {
            font-weight: bold;
            color: #28a745;
            font-size: 16px;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-categoria {
            background: #e7f3ff;
            color: #0066cc;
        }

        .badge-novo {
            background: #d4edda;
            color: #155724;
        }

        .badge-usado {
            background: #fff3cd;
            color: #856404;
        }

        .badge-recondicionado {
            background: #d1ecf1;
            color: #0c5460;
        }

        /* Ações */
        .acoes {
            display: flex;
            gap: 8px;
        }

        .btn-acao {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-editar {
            background: #17a2b8;
            color: white;
        }

        .btn-editar:hover {
            background: #138496;
        }

        .btn-excluir {
            background: #dc3545;
            color: white;
        }

        .btn-excluir:hover {
            background: #c82333;
        }

        .btn-visualizar {
            background: #6c757d;
            color: white;
        }

        .btn-visualizar:hover {
            background: #5a6268;
        }

        /* Mensagem vazia */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 80px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #6c757d;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #adb5bd;
            margin-bottom: 20px;
        }

        /* Paginação */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 30px;
            border-top: 1px solid #e9ecef;
        }

        .pagination a,
        .pagination span {
            padding: 10px 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            text-decoration: none;
            color: #495057;
            font-weight: 600;
            transition: all 0.3s;
        }

        .pagination a:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination .active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Responsivo */
        @media (max-width: 1200px) {
            .filters-container {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .filters-container {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                text-align: center;
            }

            .table-container {
                padding: 15px;
            }

            table {
                font-size: 12px;
            }

            thead th,
            tbody td {
                padding: 10px 5px;
            }

            .acoes {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-box"></i>
                Gerenciamento de Produtos
            </h1>
            <a href="cadastrar.php" class="btn-novo">
                <i class="fas fa-plus"></i>
                Novo Produto
            </a>
        </div>

        <!-- Filtros e Busca -->
        <div class="filters-section">
            <form method="GET" action="">
                <div class="filters-container">
                    <div class="filter-group">
                        <label for="busca">
                            <i class="fas fa-search"></i> Buscar Produto
                        </label>
                        <input 
                            type="text" 
                            id="busca" 
                            name="busca" 
                            placeholder="Digite o nome do produto..."
                            value="<?php echo htmlspecialchars($buscaTermo); ?>"
                        >
                    </div>

                    <div class="filter-group">
                        <label for="categoria">
                            <i class="fas fa-tags"></i> Categoria
                        </label>
                        <select id="categoria" name="categoria">
                            <option value="">Todas as Categorias</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat; ?>" 
                                    <?php echo ($categoriaFiltro === $cat) ? 'selected' : ''; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="qualidade">
                            <i class="fas fa-star"></i> Qualidade
                        </label>
                        <select id="qualidade" name="qualidade">
                            <option value="">Todas as Qualidades</option>
                            <?php foreach ($qualidades as $qual): ?>
                                <option value="<?php echo $qual; ?>" 
                                    <?php echo ($qualidadeFiltro === $qual) ? 'selected' : ''; ?>>
                                    <?php echo $qual; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <button type="submit" class="btn-filtrar">
                            <i class="fas fa-filter"></i>
                            Filtrar
                        </button>
                        <a href="?" class="btn-limpar">
                            <i class="fas fa-times"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Estatísticas -->
        <div class="stats">
            <div class="stats-info">
                <strong><?php echo $totalProdutos; ?></strong> produto(s) encontrado(s)
            </div>
            <div class="stats-info">
                Página <strong><?php echo $paginaAtual; ?></strong> de <strong><?php echo max(1, $totalPaginas); ?></strong>
            </div>
        </div>

        <!-- Tabela de Produtos -->
        <div class="table-container">
            <?php if (!empty($produtosPaginados)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Valor</th>
                            <th>Categoria</th>
                            <th>Qualidade</th>
                            <th>Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtosPaginados as $produto): ?>
                            <tr>
                                <td><?php echo $produto['id']; ?></td>
                                <td class="produto-descricao">
                                    <?php echo htmlspecialchars($produto['descricao']); ?>
                                </td>
                                <td class="produto-valor">
                                    R$ <?php echo number_format($produto['valor'], 2, ',', '.'); ?>
                                </td>
                                <td>
                                    <span class="badge badge-categoria">
                                        <?php echo htmlspecialchars($produto['categoria']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $qualidadeClass = 'badge-novo';
                                    if ($produto['qualidade'] === 'Usado') {
                                        $qualidadeClass = 'badge-usado';
                                    } elseif ($produto['qualidade'] === 'Recondicionado') {
                                        $qualidadeClass = 'badge-recondicionado';
                                    }
                                    ?>
                                    <span class="badge <?php echo $qualidadeClass; ?>">
                                        <?php echo $produto['qualidade']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if (isset($produto['data_cadastro'])) {
                                        echo date('d/m/Y', strtotime($produto['data_cadastro']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="acoes">
                                        <a href="visualizar.php?id=<?php echo $produto['id']; ?>" 
                                           class="btn-acao btn-visualizar" 
                                           title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="editar.php?id=<?php echo $produto['id']; ?>" 
                                           class="btn-acao btn-editar" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmarExclusao(<?php echo $produto['id']; ?>)" 
                                                class="btn-acao btn-excluir" 
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>Nenhum produto encontrado</h3>
                    <p>Não há produtos cadastrados ou que correspondam aos filtros aplicados.</p>
                    <a href="cadastrar.php" class="btn-novo">
                        <i class="fas fa-plus"></i>
                        Cadastrar Primeiro Produto
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
            <div class="pagination">
                <?php if ($paginaAtual > 1): ?>
                    <a href="?pagina=<?php echo $paginaAtual - 1; ?>&busca=<?php echo urlencode($buscaTermo); ?>&categoria=<?php echo urlencode($categoriaFiltro); ?>&qualidade=<?php echo urlencode($qualidadeFiltro); ?>">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <?php if ($i == $paginaAtual): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?pagina=<?php echo $i; ?>&busca=<?php echo urlencode($buscaTermo); ?>&categoria=<?php echo urlencode($categoriaFiltro); ?>&qualidade=<?php echo urlencode($qualidadeFiltro); ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($paginaAtual < $totalPaginas): ?>
                    <a href="?pagina=<?php echo $paginaAtual + 1; ?>&busca=<?php echo urlencode($buscaTermo); ?>&categoria=<?php echo urlencode($categoriaFiltro); ?>&qualidade=<?php echo urlencode($qualidadeFiltro); ?>">
                        Próxima <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function confirmarExclusao(id) {
            if (confirm('Tem certeza que deseja excluir este produto?\nEsta ação não pode ser desfeita.')) {
                window.location.href = 'excluir.php?id=' + id;
            }
        }

        // Adiciona efeito de loading ao filtrar
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.querySelector('.btn-filtrar');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Filtrando...';
            btn.disabled = true;
        });
    </script>
</body>
</html>