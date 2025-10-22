<?php
class Produto {
    private $id;
    private $descricao;
    private $valor;
    private $categoria;
    private $qualidade;
    private $dataCadastro;
    private $dataAtualizacao;
    private $conn;

    /**
     * Construtor - Inicializa a conexão com o banco
     */
    public function __construct() {
        $this->conectar();
    }

    /**
     * Estabelece conexão com o banco de dados
     */
    private $conexao;

public function __construct() {
    $this->conexao = Conexao::getInstancia();
}

public function cadastrar() {
    $sql = "INSERT INTO produtos (descricao, valor, categoria, qualidade) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $this->conexao->getConexao()->prepare($sql);
    $stmt->bind_param("sdss", $this->descricao, $this->valor, 
                      $this->categoria, $this->qualidade);
    
    return $stmt->execute();
}

    // ==================== GETTERS E SETTERS ====================
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setDescricao($descricao) {
        $this->descricao = trim($descricao);
    }

    public function getValor() {
        return $this->valor;
    }

    public function setValor($valor) {
        $this->valor = $valor;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function setCategoria($categoria) {
        $this->categoria = trim($categoria);
    }

    public function getQualidade() {
        return $this->qualidade;
    }

    public function setQualidade($qualidade) {
        $this->qualidade = trim($qualidade);
    }

    public function getDataCadastro() {
        return $this->dataCadastro;
    }

    public function getDataAtualizacao() {
        return $this->dataAtualizacao;
    }

    // ==================== MÉTODOS CRUD ====================

    /**
     * Cadastra um novo produto no banco de dados
     * @return bool
     */
    public function cadastrar() {
        try {
            $sql = "INSERT INTO produtos (descricao, valor, categoria, qualidade) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar statement: " . $this->conn->error);
                return false;
            }
            
            $stmt->bind_param(
                "sdss",
                $this->descricao,
                $this->valor,
                $this->categoria,
                $this->qualidade
            );
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $this->id = $this->conn->insert_id;
            }
            
            $stmt->close();
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Erro ao cadastrar produto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza um produto existente
     * @return bool
     */
    public function atualizar() {
        try {
            $sql = "UPDATE produtos 
                    SET descricao = ?, valor = ?, categoria = ?, qualidade = ?
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar statement: " . $this->conn->error);
                return false;
            }
            
            $stmt->bind_param(
                "sdssi",
                $this->descricao,
                $this->valor,
                $this->categoria,
                $this->qualidade,
                $this->id
            );
            
            $resultado = $stmt->execute();
            $stmt->close();
            
            return $resultado && $this->conn->affected_rows > 0;
            
        } catch (Exception $e) {
            error_log("Erro ao atualizar produto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui um produto do banco de dados
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        try {
            $sql = "DELETE FROM produtos WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar statement: " . $this->conn->error);
                return false;
            }
            
            $stmt->bind_param("i", $id);
            $resultado = $stmt->execute();
            $stmt->close();
            
            return $resultado && $this->conn->affected_rows > 0;
            
        } catch (Exception $e) {
            error_log("Erro ao excluir produto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lista todos os produtos
     * @return array
     */
    public function listar() {
        try {
            $sql = "SELECT id, descricao, valor, categoria, qualidade, 
                           data_cadastro, data_atualizacao 
                    FROM produtos 
                    ORDER BY data_cadastro DESC";
            
            $resultado = $this->conn->query($sql);
            
            if (!$resultado) {
                error_log("Erro na query listar: " . $this->conn->error);
                return [];
            }
            
            $arrayProdutos = [];
            
            while ($produto = $resultado->fetch_assoc()) {
                $arrayProdutos[] = $produto;
            }
            
            $resultado->free();
            return $arrayProdutos;
            
        } catch (Exception $e) {
            error_log("Erro ao listar produtos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca um produto por ID
     * @param int $id
     * @return array|null
     */
    public function buscarPorId($id) {
        try {
            $sql = "SELECT id, descricao, valor, categoria, qualidade, 
                           data_cadastro, data_atualizacao 
                    FROM produtos 
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar statement: " . $this->conn->error);
                return null;
            }
            
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $produto = $resultado->fetch_assoc();
            
            $stmt->close();
            return $produto;
            
        } catch (Exception $e) {
            error_log("Erro ao buscar produto por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca produtos por categoria
     * @param string $categoria
     * @return array
     */
    public function buscarPorCategoria($categoria) {
        try {
            $sql = "SELECT id, descricao, valor, categoria, qualidade, 
                           data_cadastro, data_atualizacao 
                    FROM produtos 
                    WHERE categoria = ? 
                    ORDER BY descricao";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar statement: " . $this->conn->error);
                return [];
            }
            
            $stmt->bind_param("s", $categoria);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $arrayProdutos = [];
            
            while ($produto = $resultado->fetch_assoc()) {
                $arrayProdutos[] = $produto;
            }
            
            $stmt->close();
            return $arrayProdutos;
            
        } catch (Exception $e) {
            error_log("Erro ao buscar por categoria: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca produtos por qualidade
     * @param string $qualidade
     * @return array
     */
    public function buscarPorQualidade($qualidade) {
        try {
            $sql = "SELECT id, descricao, valor, categoria, qualidade, 
                           data_cadastro, data_atualizacao 
                    FROM produtos 
                    WHERE qualidade = ? 
                    ORDER BY valor";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar statement: " . $this->conn->error);
                return [];
            }
            
            $stmt->bind_param("s", $qualidade);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $arrayProdutos = [];
            
            while ($produto = $resultado->fetch_assoc()) {
                $arrayProdutos[] = $produto;
            }
            
            $stmt->close();
            return $arrayProdutos;
            
        } catch (Exception $e) {
            error_log("Erro ao buscar por qualidade: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca produtos por faixa de preço
     * @param float $valorMin
     * @param float $valorMax
     * @return array
     */
    public function buscarPorFaixaPreco($valorMin, $valorMax) {
        try {
            $sql = "SELECT id, descricao, valor, categoria, qualidade, 
                           data_cadastro, data_atualizacao 
                    FROM produtos 
                    WHERE valor BETWEEN ? AND ? 
                    ORDER BY valor";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar statement: " . $this->conn->error);
                return [];
            }
            
            $stmt->bind_param("dd", $valorMin, $valorMax);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $arrayProdutos = [];
            
            while ($produto = $resultado->fetch_assoc()) {
                $arrayProdutos[] = $produto;
            }
            
            $stmt->close();
            return $arrayProdutos;
            
        } catch (Exception $e) {
            error_log("Erro ao buscar por faixa de preço: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Pesquisa produtos por termo na descrição
     * @param string $termo
     * @return array
     */
    public function pesquisar($termo) {
        try {
            $sql = "SELECT id, descricao, valor, categoria, qualidade, 
                           data_cadastro, data_atualizacao 
                    FROM produtos 
                    WHERE descricao LIKE ? 
                    ORDER BY descricao";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar statement: " . $this->conn->error);
                return [];
            }
            
            $termoBusca = "%{$termo}%";
            $stmt->bind_param("s", $termoBusca);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $arrayProdutos = [];
            
            while ($produto = $resultado->fetch_assoc()) {
                $arrayProdutos[] = $produto;
            }
            
            $stmt->close();
            return $arrayProdutos;
            
        } catch (Exception $e) {
            error_log("Erro ao pesquisar produtos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Conta o total de produtos cadastrados
     * @return int
     */
    public function contarProdutos() {
        try {
            $sql = "SELECT COUNT(*) as total FROM produtos";
            $resultado = $this->conn->query($sql);
            
            if ($resultado) {
                $dados = $resultado->fetch_assoc();
                return (int) $dados['total'];
            }
            
            return 0;
            
        } catch (Exception $e) {
            error_log("Erro ao contar produtos: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lista produtos com paginação
     * @param int $limite
     * @param int $offset
     * @return array
     */
    public function listarComPaginacao($limite = 10, $offset = 0) {
        try {
            $sql = "SELECT id, descricao, valor, categoria, qualidade, 
                           data_cadastro, data_atualizacao 
                    FROM produtos 
                    ORDER BY data_cadastro DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar statement: " . $this->conn->error);
                return [];
            }
            
            $stmt->bind_param("ii", $limite, $offset);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $arrayProdutos = [];
            
            while ($produto = $resultado->fetch_assoc()) {
                $arrayProdutos[] = $produto;
            }
            
            $stmt->close();
            return $arrayProdutos;
            
        } catch (Exception $e) {
            error_log("Erro ao listar com paginação: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica se um produto existe
     * @param int $id
     * @return bool
     */
    public function existe($id) {
        try {
            $sql = "SELECT 1 FROM produtos WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                return false;
            }
            
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $existe = $resultado->num_rows > 0;
            $stmt->close();
            
            return $existe;
            
        } catch (Exception $e) {
            error_log("Erro ao verificar existência: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Destrutor - Fecha a conexão
     */
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>