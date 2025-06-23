<?php

namespace App\Core;

class Controller {
    protected $view;
    protected $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
        $this->view = new View();
    }
    
    protected function render($template, $data = []) {
        return $this->view->render($template, $data);
    }
    
    protected function json($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    protected function getPost($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    protected function getQuery($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    protected function getFile($key) {
        return $_FILES[$key] ?? null;
    }
    
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            if (strpos($rule, 'required') !== false && empty($data[$field])) {
                $errors[$field] = "Field {$field} is required";
                continue;
            }
            
            if (!empty($data[$field])) {
                if (strpos($rule, 'email') !== false && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Invalid email format";
                }
                
                if (strpos($rule, 'min:') !== false) {
                    preg_match('/min:(\d+)/', $rule, $matches);
                    $min = $matches[1];
                    if (strlen($data[$field]) < $min) {
                        $errors[$field] = "Field {$field} must be at least {$min} characters";
                    }
                }
                
                if (strpos($rule, 'max:') !== false) {
                    preg_match('/max:(\d+)/', $rule, $matches);
                    $max = $matches[1];
                    if (strlen($data[$field]) > $max) {
                        $errors[$field] = "Field {$field} must not exceed {$max} characters";
                    }
                }
            }
        }
        
        return $errors;
    }
    
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
    
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
        }
    }
    
    protected function requireAdmin() {
        $this->requireAuth();
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect('/');
        }
    }
    
    protected function getCurrentUser() {
        if ($this->isAuthenticated()) {
            $userModel = new \App\Models\User();
            return $userModel->find($_SESSION['user_id']);
        }
        return null;
    }
} 