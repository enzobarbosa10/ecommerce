<?php
// Controller/UsuarioController.php

class UsuarioController {
    
    /**
     * Método para realizar o login do usuário
     */
    public function login($email, $senha) {
        // Valida se email e senha foram informados
        if (empty($email) || empty($senha)) {
            return "Por favor, preencha todos os campos.";
        }
        
        // Valida o formato do email e a senha
        if ($this->validaEmail($email) && $this->validaSenha($senha)) {
            // Cria objeto do model Usuario e tenta realizar o login
            require_once __DIR__ . '/../Models/Usuario.php';
            $objUsuario = new Usuario();
            $objUsuario->setEmail($email);
            $objUsuario->setSenha($senha);

            $resultado = $objUsuario->logar();
            
            if ($resultado) {
                return "Login realizado com sucesso!";
            } else {
                return "E-mail ou senha incorretos.";
            }
        } else {
            return "E-mail ou senha inválidos.";
        }
    }
    
    /**
     * Método para validar o formato do e-mail
     */
    private function validaEmail($email) {
        // Verifica se o email está no formato correto
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }
    
    /**
     * Método para validar a senha
     */
    private function validaSenha($senha) {
        // Valida se a senha tem pelo menos 6 caracteres
        if (strlen($senha) >= 6) {
            return true;
        }
        return false;
    }
    
    /**
     * Método para validar o nome do usuário
     */
    private function validaNome($nome) {
        // Verifica se o nome não está vazio e tem pelo menos 3 caracteres
        if (!empty($nome) && strlen($nome) >= 3) {
            return true;
        }
        return false;
    }
    
    /**
     * Método para cadastrar um novo usuário
     */
    public function cadastraUsuario($objUsuario) {
        // Obtém os dados do objeto usuário
        $nome = $objUsuario->getNome();
        $email = $objUsuario->getEmail();
        $senha = $objUsuario->getSenha();
        
        // Valida se todos os campos foram preenchidos
        if (empty($nome) || empty($email) || empty($senha)) {
            return "Por favor, preencha todos os campos.";
        }
        
        // Valida o nome
        if (!$this->validaNome($nome)) {
            return "Nome inválido. O nome deve ter pelo menos 3 caracteres.";
        }
        
        // Valida o email
        if (!$this->validaEmail($email)) {
            return "E-mail inválido. Por favor, informe um e-mail válido.";
        }
        
        // Valida a senha
        if (!$this->validaSenha($senha)) {
            return "Senha inválida. A senha deve ter pelo menos 6 caracteres.";
        }
        
        // Se todas as validações passarem, cadastra o usuário
        $resultado = $objUsuario->Cadastrar();
        
        if ($resultado) {
            return "Usuário cadastrado com sucesso!";
        } else {
            return "Erro ao cadastrar usuário. Verifique se o e-mail já está cadastrado.";
        }
    }
}
?>