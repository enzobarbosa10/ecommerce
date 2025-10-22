<?php
$path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/';
include_once($path . 'Models/Produto.php');

class ProdutoController {
    
    private $mensagensErro = [];
    
    /**
     * Cadastrar um novo produto
     * @param Produto $objProduto
     * @return array ['sucesso' => bool, 'mensagem' => string]
     */
    public function cadastrarProduto($objProduto) {
        // Limpa mensagens de erro anteriores
        $this->mensagensErro = [];
        
        // Valida todos os campos
        if (!$this->validarProduto($objProduto)) {
            return [
                'sucesso' => false,
                'mensagem' => implode('<br>', $this->mensagensErro)
            ];
        }
        
        try {
            $resultado = $objProduto->cadastrar();
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Produto cadastrado com sucesso!'
                ];
            } else {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Erro ao cadastrar produto. Tente novamente.'
                ];
            }
        } catch (Exception $e) {
            error_log("Erro ao cadastrar produto: " . $e->getMessage());
            return [
                'sucesso' => false,
                'mensagem' => 'Erro no sistema. Contate o administrador.'
            ];
        }
    }
    
    /**
     * Atualizar um produto existente
     * @param Produto $objProduto
     * @return array ['sucesso' => bool, 'mensagem' => string]
     */
    public function atualizarProduto($objProduto) {
        // Limpa mensagens de erro anteriores
        $this->mensagensErro = [];
        
        // Valida o ID
        if (empty($objProduto->getId()) || !is_numeric($objProduto->getId())) {
            return [
                'sucesso' => false,
                'mensagem' => 'ID do produto inválido.'
            ];
        }
        
        // Valida os campos
        if (!$this->validarProduto($objProduto)) {
            return [
                'sucesso' => false,
                'mensagem' => implode('<br>', $this->mensagensErro)
            ];
        }
        
        try {
            $resultado = $objProduto->atualizar();
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Produto atualizado com sucesso!'
                ];
            } else {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Produto não encontrado ou não foi possível atualizar.'
                ];
            }
        } catch (Exception $e) {
            error_log("Erro ao atualizar produto: " . $e->getMessage());
            return [
                'sucesso' => false,
                'mensagem' => 'Erro no sistema. Contate o administrador.'
            ];
        }
    }
    
    /**
     * Excluir um produto
     * @param int $id
     * @return array ['sucesso' => bool, 'mensagem' => string]
     */
    public function excluirProduto($id) {
        if (empty($id) || !is_numeric($id)) {
            return [
                'sucesso' => false,
                'mensagem' => 'ID do produto inválido.'
            ];
        }
        
        try {
            $objProduto = new Produto();
            $resultado = $objProduto->excluir($id);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Produto excluído com sucesso!'
                ];
            } else {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Produto não encontrado ou não foi possível excluir.'
                ];
            }
        } catch (Exception $e) {
            error_log("Erro ao excluir produto: " . $e->getMessage());
            return [
                'sucesso' => false,
                'mensagem' => 'Erro no sistema. Contate o administrador.'
            ];
        }
    }
    
    /**
     * Listar todos os produtos
     * @return array
     */
    public function listarProdutos() {
        try {
            $objProduto = new Produto();
            return $objProduto->listar();
        } catch (Exception $e) {
            error_log("Erro ao listar produtos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar produto por ID
     * @param int $id
     * @return array|null
     */
    public function buscarProdutoPorId($id) {
        if (empty($id) || !is_numeric($id)) {
            return null;
        }
        
        try {
            $objProduto = new Produto();
            return $objProduto->buscarPorId($id);
        } catch (Exception $e) {
            error_log("Erro ao buscar produto: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Buscar produtos por categoria
     * @param string $categoria
     * @return array
     */
    public function buscarPorCategoria($categoria) {
        if (empty($categoria)) {
            return [];
        }
        
        try {
            $objProduto = new Produto();
            return $objProduto->buscarPorCategoria($categoria);
        } catch (Exception $e) {
            error_log("Erro ao buscar por categoria: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar produtos por qualidade
     * @param string $qualidade
     * @return array
     */
    public function buscarPorQualidade($qualidade) {
        if (empty($qualidade)) {
            return [];
        }
        
        try {
            $objProduto = new Produto();
            return $objProduto->buscarPorQualidade($qualidade);
        } catch (Exception $e) {
            error_log("Erro ao buscar por qualidade: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar produtos por faixa de preço
     * @param float $valorMin
     * @param float $valorMax
     * @return array
     */
    public function buscarPorFaixaPreco($valorMin, $valorMax) {
        if (!is_numeric($valorMin) || !is_numeric($valorMax) || $valorMin > $valorMax) {
            return [];
        }
        
        try {
            $objProduto = new Produto();
            return $objProduto->buscarPorFaixaPreco($valorMin, $valorMax);
        } catch (Exception $e) {
            error_log("Erro ao buscar por faixa de preço: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Pesquisar produtos por termo de busca
     * @param string $termo
     * @return array
     */
    public function pesquisarProdutos($termo) {
        if (empty($termo) || strlen($termo) < 3) {
            return [];
        }
        
        try {
            $objProduto = new Produto();
            return $objProduto->pesquisar($termo);
        } catch (Exception $e) {
            error_log("Erro ao pesquisar produtos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Valida todos os campos do produto
     * @param Produto $objProduto
     * @return bool
     */
    private function validarProduto($objProduto) {
        $valido = true;
        
        // Valida descrição
        if (!$this->validarDescricao($objProduto->getDescricao())) {
            $valido = false;
        }
        
        // Valida valor
        if (!$this->validarValor($objProduto->getValor())) {
            $valido = false;
        }
        
        // Valida categoria
        if (!$this->validarCategoria($objProduto->getCategoria())) {
            $valido = false;
        }
        
        // Valida qualidade
        if (!$this->validarQualidade($objProduto->getQualidade())) {
            $valido = false;
        }
        
        return $valido;
    }
    
    /**
     * Valida a descrição do produto
     * @param string $descricao
     * @return bool
     */
    private function validarDescricao($descricao) {
        if (empty($descricao)) {
            $this->mensagensErro[] = "A descrição é obrigatória.";
            return false;
        }
        
        if (strlen($descricao) < 3) {
            $this->mensagensErro[] = "A descrição deve ter pelo menos 3 caracteres.";
            return false;
        }
        
        if (strlen($descricao) > 255) {
            $this->mensagensErro[] = "A descrição não pode ter mais de 255 caracteres.";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida o valor do produto
     * @param mixed $valor
     * @return bool
     */
    private function validarValor($valor) {
        if (empty($valor) && $valor !== 0 && $valor !== '0') {
            $this->mensagensErro[] = "O valor é obrigatório.";
            return false;
        }
        
        if (!is_numeric($valor)) {
            $this->mensagensErro[] = "O valor deve ser numérico.";
            return false;
        }
        
        if ($valor < 0) {
            $this->mensagensErro[] = "O valor não pode ser negativo.";
            return false;
        }
        
        if ($valor > 999999.99) {
            $this->mensagensErro[] = "O valor é muito alto. Máximo permitido: R$ 999.999,99";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida a categoria do produto
     * @param string $categoria
     * @return bool
     */
    private function validarCategoria($categoria) {
        if (empty($categoria)) {
            $this->mensagensErro[] = "A categoria é obrigatória.";
            return false;
        }
        
        // Lista de categorias válidas (você pode adaptar conforme necessário)
        $categoriasValidas = [
            'Eletrônicos',
            'Roupas',
            'Alimentos',
            'Livros',
            'Móveis',
            'Brinquedos',
            'Esportes',
            'Beleza',
            'Saúde',
            'Automotivo',
            'Outros'
        ];
        
        if (!in_array($categoria, $categoriasValidas)) {
            $this->mensagensErro[] = "Categoria inválida. Escolha uma categoria válida.";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida a qualidade do produto
     * @param string $qualidade
     * @return bool
     */
    private function validarQualidade($qualidade) {
        if (empty($qualidade)) {
            $this->mensagensErro[] = "A qualidade é obrigatória.";
            return false;
        }
        
        // Lista de qualidades válidas
        $qualidadesValidas = ['Novo', 'Usado', 'Recondicionado'];
        
        if (!in_array($qualidade, $qualidadesValidas)) {
            $this->mensagensErro[] = "Qualidade inválida. Opções: Novo, Usado ou Recondicionado.";
            return false;
        }
        
        return true;
    }
    
    /**
     * Retorna as categorias disponíveis
     * @return array
     */
    public function listarCategorias() {
        return [
            'Eletrônicos',
            'Roupas',
            'Alimentos',
            'Livros',
            'Móveis',
            'Brinquedos',
            'Esportes',
            'Beleza',
            'Saúde',
            'Automotivo',
            'Outros'
        ];
    }
    
    /**
     * Retorna as qualidades disponíveis
     * @return array
     */
    public function listarQualidades() {
        return ['Novo', 'Usado', 'Recondicionado'];
    }
    
    /**
     * Retorna as mensagens de erro
     * @return array
     */
    public function getMensagensErro() {
        return $this->mensagensErro;
    }
}
?>