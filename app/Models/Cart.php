<?php

namespace App\Models;

use App\Core\Model;

class Cart extends Model {
    protected $table = 'cart';
    
    public function getItems($userId) {
        $sql = "SELECT c.*, p.name, p.price, p.image, p.stock 
                FROM {$this->table} c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ? 
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    public function addItem($userId, $productId, $quantity = 1) {
        // Check if product exists and has enough stock
        $productModel = new Product();
        $product = $productModel->find($productId);
        
        if (!$product || $product['stock'] < $quantity) {
            return false;
        }
        
        // Check if item already exists in cart
        $existingItem = $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        );
        
        if ($existingItem) {
            // Update quantity if item exists
            $newQuantity = $existingItem['quantity'] + $quantity;
            
            if ($product['stock'] < $newQuantity) {
                return false;
            }
            
            return $this->update($existingItem['id'], [
                'quantity' => $newQuantity
            ]);
        } else {
            // Add new item
            return $this->create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }
    
    public function updateQuantity($userId, $productId, $quantity) {
        // Check if product exists and has enough stock
        $productModel = new Product();
        $product = $productModel->find($productId);
        
        if (!$product || $product['stock'] < $quantity) {
            return false;
        }
        
        // Update quantity
        return $this->db->update(
            $this->table,
            ['quantity' => $quantity],
            'user_id = ? AND product_id = ?',
            [$userId, $productId]
        );
    }
    
    public function removeItem($userId, $productId) {
        return $this->db->delete(
            $this->table,
            'user_id = ? AND product_id = ?',
            [$userId, $productId]
        );
    }
    
    public function clearCart($userId) {
        return $this->db->delete(
            $this->table,
            'user_id = ?',
            [$userId]
        );
    }
    
    public function getTotal($userId) {
        $sql = "SELECT SUM(c.quantity * p.price) as total 
                FROM {$this->table} c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?";
        
        $result = $this->db->fetch($sql, [$userId]);
        return $result['total'] ?? 0;
    }
    
    public function getItemCount($userId) {
        $sql = "SELECT SUM(quantity) as count FROM {$this->table} WHERE user_id = ?";
        $result = $this->db->fetch($sql, [$userId]);
        return $result['count'] ?? 0;
    }
    
    public function validateStock($userId) {
        $items = $this->getItems($userId);
        $errors = [];
        
        foreach ($items as $item) {
            if ($item['stock'] < $item['quantity']) {
                $errors[] = "Not enough stock for {$item['name']}. Available: {$item['stock']}";
            }
        }
        
        return $errors;
    }
} 