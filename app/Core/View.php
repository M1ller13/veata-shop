<?php

namespace App\Core;

class View {
    private $layout = 'default';
    private $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
    }
    
    public function setLayout($layout) {
        $this->layout = $layout;
    }
    
    public function render($template, $data = []) {
        $templatePath = __DIR__ . '/../../resources/views/' . $template . '.php';
        $layoutPath = __DIR__ . '/../../resources/views/layouts/' . $this->layout . '.php';
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Template {$template} not found");
        }
        
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout {$this->layout} not found");
        }
        
        // Extract data to make variables available in template
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include template
        require $templatePath;
        
        // Get template content
        $content = ob_get_clean();
        
        // Include layout with content
        require $layoutPath;
    }
    
    public function partial($template, $data = []) {
        $templatePath = __DIR__ . '/../../resources/views/partials/' . $template . '.php';
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Partial {$template} not found");
        }
        
        extract($data);
        
        ob_start();
        require $templatePath;
        return ob_get_clean();
    }
    
    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public function asset($path) {
        return $this->config['app']['url'] . '/assets/' . ltrim($path, '/');
    }
    
    public function url($path) {
        return $this->config['app']['url'] . '/' . ltrim($path, '/');
    }
    
    public function csrf() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public function csrfField() {
        return '<input type="hidden" name="csrf_token" value="' . $this->csrf() . '">';
    }
    
    public function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }
    
    public function errors($key) {
        return $_SESSION['errors'][$key] ?? [];
    }
    
    public function hasErrors($key) {
        return !empty($_SESSION['errors'][$key]);
    }
    
    public function flash($key) {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    public function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }
} 