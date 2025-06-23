<?php

namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'users';
    
    public function findByEmail($email) {
        return $this->findBy('email', $email);
    }
    
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    public function register($data) {
        $errors = [];
        
        // Check if email already exists
        if ($this->findByEmail($data['email'])) {
            $errors['email'] = 'Email already exists';
            return ['success' => false, 'errors' => $errors];
        }
        
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Set default role
        $data['role'] = 'user';
        
        // Create user
        $userId = $this->create($data);
        
        return [
            'success' => true,
            'user_id' => $userId
        ];
    }
    
    public function updateProfile($id, $data) {
        $errors = [];
        
        // Check if email is being changed and if it already exists
        if (isset($data['email'])) {
            $existingUser = $this->findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                $errors['email'] = 'Email already exists';
                return ['success' => false, 'errors' => $errors];
            }
        }
        
        // Hash password if it's being updated
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        // Update user
        $this->update($id, $data);
        
        return ['success' => true];
    }
    
    public function getOrders($userId) {
        $sql = "SELECT o.*, 
                       COUNT(oi.id) as item_count,
                       SUM(oi.quantity) as total_items
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    public function getOrderDetails($userId, $orderId) {
        $sql = "SELECT o.*, 
                       oi.quantity,
                       oi.price as item_price,
                       p.name as product_name,
                       p.image as product_image
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE o.user_id = ? AND o.id = ?
                ORDER BY oi.id";
        
        return $this->db->fetchAll($sql, [$userId, $orderId]);
    }
} 