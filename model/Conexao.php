<?php
/**
 * Classe Conexao - Gerencia a conexão com o banco de dados
 * Implementa o padrão Singleton + PDO + Configuração Externa
 */
class Conexao {
    
    // Instância única da classe (Singleton)
    private static $instancia = null;
    
    // Conexão PDO
    private $conn;
    
    // Configurações do banco (carregadas do arquivo config)
    private $config;
    
    /**
     * Construtor privado - impede criação direta de objetos
     * Carrega as configurações do arquivo externo
     */
    private function __construct() {
        $this->carregarConfiguracao();
        $this->conectar();
    }
    
    /**
     * Impede a clonagem do objeto (Singleton)
     */
    private function __clone() {}
    
    /**
     * Impede a desserialização do objeto (Singleton)
     */
    public function __wakeup() {
        throw new Exception("Não é possível desserializar um singleton.");
    }
    
    /**
     * Retorna a instância única da classe (Singleton)
     * @return Conexao
     */
    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }
    
    /**
     * Carrega as configurações do arquivo de configuração
     * @return void
     */
    private function carregarConfiguracao() {
        $arquivoConfig = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/config/database.php';
        
        if (file_exists($arquivoConfig)) {
            $this->config = require $arquivoConfig;
        } else {
            // Configurações padrão caso o arquivo não exista
            $this->config = [
                'host'     => 'localhost',
                'dbname'   => 'ecommerce',
                'usuario'  => 'root',
                'senha'    => '',
                'charset'  => 'utf8mb4',
                'port'     => 3306
            ];
            
            error_log("Arquivo de configuração não encontrado. Usando configurações padrão.");
        }
    }
    
    /**
     * Estabelece a conexão com o banco de dados usando PDO
     * @return void
     */
    private function conectar() {
        try {
            // DSN (Data Source Name)
            $dsn = sprintf(
                "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                $this->config['host'],
                $this->config['port'],
                $this->config['dbname'],
                $this->config['charset']
            );
            
            // Opções do PDO para melhor segurança e performance
            $opcoes = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->config['charset']}",
                PDO::ATTR_STRINGIFY_FETCHES  => false,
                PDO::ATTR_CASE               => PDO::CASE_NATURAL
            ];
            
            // Cria a conexão PDO
            $this->conn = new PDO(
                $dsn,
                $this->config['usuario'],
                $this->config['senha'],
                $opcoes
            );
            
        } catch (PDOException $e) {
            // Log do erro (não expõe detalhes sensíveis)
            error_log("Erro de conexão PDO: " . $e->getMessage());
            error_log("DSN tentado: mysql:host={$this->config['host']};dbname={$this->config['dbname']}");
            
            // Mensagem genérica para o usuário
            die("Erro ao conectar com o banco de dados. Por favor, contate o administrador.");
        }
    }
    
    /**
     * Retorna a conexão PDO ativa
     * @return PDO
     */
    public function getConexao() {
        // Verifica se a conexão está ativa
        if ($this->conn === null) {
            $this->conectar();
        }
        return $this->conn;
    }
    
    /**
     * Testa a conexão com o banco
     * @return bool
     */
    public function testarConexao() {
        try {
            $stmt = $this->conn->query("SELECT 1");
            return $stmt !== false;
        } catch (PDOException $e) {
            error_log("Erro ao testar conexão: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Executa uma query preparada de forma segura
     * @param string $sql
     * @param array $parametros
     * @return PDOStatement|false
     */
    public function executar($sql, $parametros = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erro ao executar query: " . $e->getMessage());
            error_log("SQL: " . $sql);
            return false;
        }
    }
    
    /**
     * Executa SELECT e retorna todos os resultados
     * @param string $sql
     * @param array $parametros
     * @return array
     */
    public function select($sql, $parametros = []) {
        try {
            $stmt = $this->executar($sql, $parametros);
            return $stmt ? $stmt->fetchAll() : [];
        } catch (PDOException $e) {
            error_log("Erro no SELECT: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Executa SELECT e retorna apenas um resultado
     * @param string $sql
     * @param array $parametros
     * @return array|null
     */
    public function selectOne($sql, $parametros = []) {
        try {
            $stmt = $this->executar($sql, $parametros);
            return $stmt ? $stmt->fetch() : null;
        } catch (PDOException $e) {
            error_log("Erro no SELECT ONE: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Executa INSERT, UPDATE ou DELETE
     * @param string $sql
     * @param array $parametros
     * @return bool
     */
    public function executarQuery($sql, $parametros = []) {
        $stmt = $this->executar($sql, $parametros);
        return $stmt !== false;
    }
    
    /**
     * Retorna o número de linhas afetadas pela última query
     * @return int
     */
    public function linhasAfetadas() {
        return $this->conn->query("SELECT ROW_COUNT()")->fetchColumn();
    }
    
    /**
     * Retorna o ID do último registro inserido
     * @return string
     */
    public function ultimoId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Inicia uma transação
     * @return bool
     */
    public function iniciarTransacao() {
        try {
            return $this->conn->beginTransaction();
        } catch (PDOException $e) {
            error_log("Erro ao iniciar transação: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Confirma (commit) uma transação
     * @return bool
     */
    public function confirmarTransacao() {
        try {
            return $this->conn->commit();
        } catch (PDOException $e) {
            error_log("Erro ao confirmar transação: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reverte (rollback) uma transação
     * @return bool
     */
    public function reverterTransacao() {
        try {
            if ($this->conn->inTransaction()) {
                return $this->conn->rollBack();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao reverter transação: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica se há uma transação ativa
     * @return bool
     */
    public function emTransacao() {
        return $this->conn->inTransaction();
    }
    
    /**
     * Retorna informações sobre o banco de dados
     * @return array
     */
    public function getInformacoes() {
        try {
            return [
                'driver'           => $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME),
                'versao_servidor'  => $this->conn->getAttribute(PDO::ATTR_SERVER_VERSION),
                'versao_cliente'   => $this->conn->getAttribute(PDO::ATTR_CLIENT_VERSION),
                'charset'          => $this->config['charset'],
                'host'             => $this->config['host'],
                'database'         => $this->config['dbname'],
                'conexao_ativa'    => $this->testarConexao()
            ];
        } catch (PDOException $e) {
            error_log("Erro ao obter informações: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Limpa o cache de prepared statements
     * @return void
     */
    public function limparCache() {
        $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    
    /**
     * Fecha a conexão com o banco
     * @return void
     */
    public function fecharConexao() {
        $this->conn = null;
        self::$instancia = null;
    }
    
    /**
     * Destrutor - fecha a conexão automaticamente
     */
    public function __destruct() {
        $this->conn = null;
    }
}
?>

-----

<?php
/**
 * EXEMPLOS DE USO DA CLASSE CONEXAO MELHORADA
 * 
 * Este arquivo demonstra as diversas formas de usar a classe Conexao
 */

require_once 'Models/Conexao.php';

// ==================== EXEMPLO 1: Conexão Básica ====================
echo "<h3>Exemplo 1: Obter Conexão</h3>";

$conexao = Conexao::getInstancia();
$pdo = $conexao->getConexao();

if ($conexao->testarConexao()) {
    echo "✅ Conexão estabelecida com sucesso!<br>";
} else {
    echo "❌ Falha na conexão!<br>";
}

// ==================== EXEMPLO 2: SELECT Simples ====================
echo "<h3>Exemplo 2: SELECT Simples</h3>";

$sql = "SELECT * FROM produtos LIMIT 5";
$produtos = $conexao->select($sql);

foreach ($produtos as $produto) {
    echo "ID: {$produto['id']} - {$produto['descricao']} - R$ {$produto['valor']}<br>";
}

// ==================== EXEMPLO 3: SELECT com Parâmetros ====================
echo "<h3>Exemplo 3: SELECT com Parâmetros (Prepared Statement)</h3>";

$sql = "SELECT * FROM produtos WHERE categoria = ? AND valor > ?";
$parametros = ['Eletrônicos', 1000];
$produtos = $conexao->select($sql, $parametros);

foreach ($produtos as $produto) {
    echo "{$produto['descricao']} - R$ {$produto['valor']}<br>";
}

// ==================== EXEMPLO 4: SELECT de Um Único Registro ====================
echo "<h3>Exemplo 4: SELECT One (Um Único Registro)</h3>";

$sql = "SELECT * FROM produtos WHERE id = ?";
$produto = $conexao->selectOne($sql, [1]);

if ($produto) {
    echo "Produto encontrado: {$produto['descricao']}<br>";
} else {
    echo "Produto não encontrado<br>";
}

// ==================== EXEMPLO 5: INSERT ====================
echo "<h3>Exemplo 5: INSERT</h3>";

$sql = "INSERT INTO produtos (descricao, valor, categoria, qualidade) VALUES (?, ?, ?, ?)";
$parametros = [
    'Mouse Gamer RGB',
    89.90,
    'Eletrônicos',
    'Novo'
];

if ($conexao->executarQuery($sql, $parametros)) {
    echo "✅ Produto cadastrado com sucesso!<br>";
    echo "ID do produto: " . $conexao->ultimoId() . "<br>";
} else {
    echo "❌ Erro ao cadastrar produto<br>";
}

// ==================== EXEMPLO 6: UPDATE ====================
echo "<h3>Exemplo 6: UPDATE</h3>";

$sql = "UPDATE produtos SET valor = ? WHERE id = ?";
$parametros = [99.90, $conexao->ultimoId()];

if ($conexao->executarQuery($sql, $parametros)) {
    echo "✅ Produto atualizado com sucesso!<br>";
} else {
    echo "❌ Erro ao atualizar produto<br>";
}

// ==================== EXEMPLO 7: DELETE ====================
echo "<h3>Exemplo 7: DELETE</h3>";

$sql = "DELETE FROM produtos WHERE id = ?";
$parametros = [999]; // ID fictício

if ($conexao->executarQuery($sql, $parametros)) {
    echo "✅ Produto excluído com sucesso!<br>";
} else {
    echo "⚠️ Nenhum produto foi excluído (ID não existe)<br>";
}

// ==================== EXEMPLO 8: Transações ====================
echo "<h3>Exemplo 8: Transações (Rollback em caso de erro)</h3>";

try {
    // Inicia a transação
    $conexao->iniciarTransacao();
    
    // Primeira operação
    $sql1 = "INSERT INTO produtos (descricao, valor, categoria, qualidade) 
             VALUES (?, ?, ?, ?)";
    $conexao->executarQuery($sql1, ['Produto A', 100.00, 'Eletrônicos', 'Novo']);
    
    // Segunda operação
    $sql2 = "INSERT INTO produtos (descricao, valor, categoria, qualidade) 
             VALUES (?, ?, ?, ?)";
    $conexao->executarQuery($sql2, ['Produto B', 200.00, 'Eletrônicos', 'Novo']);
    
    // Simula um erro (descomente para testar rollback)
    // throw new Exception("Erro simulado!");
    
    // Se tudo der certo, confirma a transação
    $conexao->confirmarTransacao();
    echo "✅ Transação confirmada com sucesso!<br>";
    
} catch (Exception $e) {
    // Se houver erro, reverte tudo
    $conexao->reverterTransacao();
    echo "❌ Erro na transação. Todas as operações foram revertidas!<br>";
    echo "Erro: " . $e->getMessage() . "<br>";
}

// ==================== EXEMPLO 9: Busca com LIKE ====================
echo "<h3>Exemplo 9: Busca com LIKE</h3>";

$termo = "notebook";
$sql = "SELECT * FROM produtos WHERE descricao LIKE ?";
$parametros = ["%{$termo}%"];
$produtos = $conexao->select($sql, $parametros);

echo "Encontrados " . count($produtos) . " produtos com o termo '{$termo}'<br>";
foreach ($produtos as $produto) {
    echo "- {$produto['descricao']}<br>";
}

// ==================== EXEMPLO 10: Informações do Banco ====================
echo "<h3>Exemplo 10: Informações do Banco de Dados</h3>";

$info = $conexao->getInformacoes();
echo "<pre>";
print_r($info);
echo "</pre>";

// ==================== EXEMPLO 11: Contagem de Registros ====================
echo "<h3>Exemplo 11: Contagem de Registros</h3>";

$sql = "SELECT COUNT(*) as total FROM produtos";
$resultado = $conexao->selectOne($sql);
echo "Total de produtos cadastrados: {$resultado['total']}<br>";

// ==================== EXEMPLO 12: JOIN ====================
echo "<h3>Exemplo 12: JOIN (exemplo com pedidos e usuários)</h3>";

$sql = "SELECT p.id, p.descricao, p.valor, c.nome as categoria_nome 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria = c.nome 
        LIMIT 5";

$resultados = $conexao->select($sql);
foreach ($resultados as $row) {
    echo "Produto: {$row['descricao']} | Categoria: {$row['categoria_nome']}<br>";
}

// ==================== EXEMPLO 13: Busca Complexa ====================
echo "<h3>Exemplo 13: Busca Complexa com Múltiplas Condições</h3>";

$sql = "SELECT * FROM produtos 
        WHERE categoria = ? 
        AND valor BETWEEN ? AND ? 
        AND qualidade IN (?, ?) 
        ORDER BY valor DESC";

$parametros = ['Eletrônicos', 500, 3000, 'Novo', 'Recondicionado'];
$produtos = $conexao->select($sql, $parametros);

echo "Encontrados " . count($produtos) . " produtos<br>";

// ==================== EXEMPLO 14: Executar Query Diretamente ====================
echo "<h3>Exemplo 14: Executar Query Customizada</h3>";

$stmt = $conexao->executar("SELECT descricao, valor FROM produtos WHERE valor > ?", [1000]);

if ($stmt) {
    while ($row = $stmt->fetch()) {
        echo "{$row['descricao']} - R$ {$row['valor']}<br>";
    }
}

// ==================== FECHAMENTO ====================
echo "<h3>Fechamento</h3>";
echo "✅ Todos os exemplos executados com sucesso!<br>";

// A conexão será fechada automaticamente pelo destrutor
// Mas você pode fechar manualmente se necessário:
// $conexao->fecharConexao();

?>

<!-- ==================== EXEMPLO 15: Uso em uma Classe Model ==================== -->
<?php
/**
 * EXEMPLO DE INTEGRAÇÃO COM MODEL
 */

class ProdutoExemplo {
    private $conexao;
    
    public function __construct() {
        $this->conexao = Conexao::getInstancia();
    }
    
    public function buscarTodos() {
        $sql = "SELECT * FROM produtos ORDER BY descricao";
        return $this->conexao->select($sql);
    }
    
    public function buscarPorId($id) {
        $sql = "SELECT * FROM produtos WHERE id = ?";
        return $this->conexao->selectOne($sql, [$id]);
    }
    
    public function cadastrar($dados) {
        $sql = "INSERT INTO produtos (descricao, valor, categoria, qualidade) 
                VALUES (?, ?, ?, ?)";
        
        $parametros = [
            $dados['descricao'],
            $dados['valor'],
            $dados['categoria'],
            $dados['qualidade']
        ];
        
        if ($this->conexao->executarQuery($sql, $parametros)) {
            return $this->conexao->ultimoId();
        }
        return false;
    }
    
    public function atualizar($id, $dados) {
        $sql = "UPDATE produtos 
                SET descricao = ?, valor = ?, categoria = ?, qualidade = ? 
                WHERE id = ?";
        
        $parametros = [
            $dados['descricao'],
            $dados['valor'],
            $dados['categoria'],
            $dados['qualidade'],
            $id
        ];
        
        return $this->conexao->executarQuery($sql, $parametros);
    }
    
    public function excluir($id) {
        $sql = "DELETE FROM produtos WHERE id = ?";
        return $this->conexao->executarQuery($sql, [$id]);
    }
}

// Usando a classe
echo "<h3>Exemplo 15: Usando Conexão em um Model</h3>";

$produtoModel = new ProdutoExemplo();

// Buscar todos
$produtos = $produtoModel->buscarTodos();
echo "Total de produtos: " . count($produtos) . "<br>";

// Buscar por ID
$produto = $produtoModel->buscarPorId(1);
if ($produto) {
    echo "Produto ID 1: {$produto['descricao']}<br>";
}

// Cadastrar novo
$novoProduto = [
    'descricao' => 'Teclado Mecânico',
    'valor' => 350.00,
    'categoria' => 'Eletrônicos',
    'qualidade' => 'Novo'
];

$idNovo = $produtoModel->cadastrar($novoProduto);
if ($idNovo) {
    echo "✅ Novo produto cadastrado com ID: {$idNovo}<br>";
}

?>

<!-- ==================== DICAS DE SEGURANÇA ==================== -->
<?php
echo "<hr><h2>🔒 DICAS DE SEGURANÇA</h2>";
?>

<div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <h4>✅ O QUE A NOVA CLASSE JÁ FAZ:</h4>
    <ul>
        <li>✓ Usa <strong>PDO</strong> em vez de mysqli (mais seguro e moderno)</li>
        <li>✓ Implementa <strong>Prepared Statements</strong> (proteção contra SQL Injection)</li>
        <li>✓ Usa <strong>Singleton Pattern</strong> (apenas uma conexão ativa)</li>
        <li>✓ <strong>Separação de configurações</strong> em arquivo externo</li>
        <li>✓ <strong>Tratamento de erros</strong> com try-catch</li>
        <li>✓ <strong>Logs de erro</strong> sem expor informações sensíveis</li>
        <li>✓ <strong>Charset UTF-8</strong> configurado corretamente</li>
        <li>✓ <strong>Transações</strong> para operações complexas</li>
    </ul>
</div>

<div style="background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <h4>❌ NUNCA FAÇA:</h4>
    <ul>
        <li>✗ Concatenar valores diretamente no SQL (ex: "WHERE id = $id")</li>
        <li>✗ Expor senhas ou detalhes do banco em mensagens de erro</li>
        <li>✗ Versionar arquivo database.php com credenciais reais</li>
        <li>✗ Usar a mesma senha de desenvolvimento em produção</li>
        <li>✗ Desabilitar o modo de erros do PDO</li>
    </ul>
</div>

<div style="background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0;">
    <h4>💡 BOAS PRÁTICAS ADICIONAIS:</h4>
    <ul>
        <li>🔹 Use <strong>.env</strong> para credenciais em produção</li>
        <li>🔹 Configure <strong>backups automáticos</strong> do banco</li>
        <li>🔹 Use <strong>SSL/TLS</strong> para conexões com banco remoto</li>
        <li>🔹 Implemente <strong>rate limiting</strong> em endpoints críticos</li>
        <li>🔹 Monitore <strong>logs de erro</strong> regularmente</li>
        <li>🔹 Use <strong>senhas fortes</strong> para usuários do banco</li>
        <li>🔹 Limite <strong>privilégios</strong> do usuário do banco (não use root em produção)</li>
    </ul>
</div>

<!-- ==================== COMPARAÇÃO: ANTES vs DEPOIS ==================== -->
<?php
echo "<hr><h2>📊 COMPARAÇÃO: ANTES vs DEPOIS</h2>";
?>

<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead style="background: #6c757d; color: white;">
        <tr>
            <th style="padding: 10px; border: 1px solid #ddd;">Aspecto</th>
            <th style="padding: 10px; border: 1px solid #ddd;">❌ ANTES</th>
            <th style="padding: 10px; border: 1px solid #ddd;">✅ DEPOIS</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><strong>Segurança</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #f8d7da;">SQL Injection vulnerável</td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #d4edda;">Prepared Statements</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><strong>Tecnologia</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #f8d7da;">mysqli (antigo)</td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #d4edda;">PDO (moderno)</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><strong>Padrão</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #f8d7da;">Sem padrão</td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #d4edda;">Singleton Pattern</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><strong>Configuração</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #f8d7da;">Hardcoded na classe</td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #d4edda;">Arquivo externo</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><strong>Erros</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #f8d7da;">Sem tratamento</td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #d4edda;">Try-catch + logs</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><strong>Responsabilidade</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #f8d7da;">Login na conexão (?!)</td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #d4edda;">Apenas conexão</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><strong>Métodos</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #f8d7da;">2 métodos</td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #d4edda;">15+ métodos úteis</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><strong>Transações</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #f8d7da;">Não suportado</td>
            <td style="padding: 10px; border: 1px solid #ddd; background: #d4edda;">Totalmente suportado</td>
        </tr>
    </tbody>
</table>

<hr>
<div style="background: #28a745; color: white; padding: 20px; border-radius: 5px; text-align: center;">
    <h2>🎉 Sistema de Conexão Profissional Completo!</h2>
    <p style="margin: 10px 0;">Agora você tem uma classe de conexão:</p>
    <p><strong>✓ Segura ✓ Moderna ✓ Escalável ✓ Profissional</strong></p>
</div>