<?php

namespace App\Models;

use App\Core\Model;

class Product extends Model {
    protected $table = 'products';
    
    public function getByCategory($categoryId, $page = 1, $perPage = 12) {
        return $this->paginate(
            $page,
            $perPage,
            'category_id = ?',
            [$categoryId],
            'created_at DESC'
        );
    }
    
    public function search($query, $page = 1, $perPage = 12) {
        return $this->paginate(
            $page,
            $perPage,
            'name LIKE ? OR description LIKE ?',
            ["%{$query}%", "%{$query}%"],
            'created_at DESC'
        );
    }
    
    public function getFeatured($limit = 4) {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    public function updateStock($id, $quantity) {
        $sql = "UPDATE {$this->table} SET stock = stock - ? WHERE id = ? AND stock >= ?";
        return $this->db->query($sql, [$quantity, $id, $quantity])->rowCount() > 0;
    }
    
    public function getWithCategory($id) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    public function getAllWithCategories($page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $items = $this->db->fetchAll($sql, [$perPage, $offset]);
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $total = $this->db->fetch($countSql)['total'];
        
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
} 