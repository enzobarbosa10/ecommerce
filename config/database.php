<?php
/**
 * Arquivo de Configuração do Banco de Dados
 * 
 * IMPORTANTE: Adicione este arquivo ao .gitignore para não versionar credenciais
 * 
 * Para ambientes diferentes (desenvolvimento, produção), 
 * crie arquivos separados: database.dev.php, database.prod.php
 */

// Define o ambiente atual (development, production, testing)
define('AMBIENTE', 'development');

// Configurações de acordo com o ambiente
$config = [];

// Configurações de Desenvolvimento
$config['development'] = [
    'host'     => 'localhost',
    'dbname'   => 'ecommerce',
    'usuario'  => 'root',
    'senha'    => '',
    'charset'  => 'utf8mb4',
    'port'     => 3306
];

// Configurações de Produção
$config['production'] = [
    'host'     => 'localhost',
    'dbname'   => 'ecommerce_prod',
    'usuario'  => 'ecommerce_user',
    'senha'    => 'senha_segura_aqui',
    'charset'  => 'utf8mb4',
    'port'     => 3306
];

// Configurações de Testes
$config['testing'] = [
    'host'     => 'localhost',
    'dbname'   => 'ecommerce_test',
    'usuario'  => 'root',
    'senha'    => '',
    'charset'  => 'utf8mb4',
    'port'     => 3306
];

// Retorna a configuração do ambiente atual
return $config[AMBIENTE];
?>