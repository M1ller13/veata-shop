<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model {
    protected $table = 'categories';
    
    public function getWithProducts($id, $page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM {$this->table} c 
                LEFT JOIN products p ON c.id = p.category_id 
                WHERE c.id = ? 
                GROUP BY c.id";
        
        $category = $this->db->fetch($sql, [$id]);
        
        if ($category) {
            $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.category_id = ? 
                    ORDER BY p.created_at DESC 
                    LIMIT ? OFFSET ?";
            
            $products = $this->db->fetchAll($sql, [$id, $perPage, $offset]);
            
            $countSql = "SELECT COUNT(*) as total FROM products WHERE category_id = ?";
            $total = $this->db->fetch($countSql, [$id])['total'];
            
            $category['products'] = [
                'items' => $products,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ];
        }
        
        return $category;
    }
    
    public function getAllWithCounts() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM {$this->table} c 
                LEFT JOIN products p ON c.id = p.category_id 
                GROUP BY c.id 
                ORDER BY c.name ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    public function getHierarchy() {
        $sql = "SELECT * FROM {$this->table} ORDER BY parent_id, name";
        $categories = $this->db->fetchAll($sql);
        
        $hierarchy = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] === null) {
                $hierarchy[$category['id']] = [
                    'category' => $category,
                    'children' => []
                ];
            }
        }
        
        foreach ($categories as $category) {
            if ($category['parent_id'] !== null && isset($hierarchy[$category['parent_id']])) {
                $hierarchy[$category['parent_id']]['children'][] = $category;
            }
        }
        
        return $hierarchy;
    }
    
    public function createSlug($name) {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check if slug exists
        $existing = $this->findBy('slug', $slug);
        if ($existing) {
            $counter = 1;
            while ($this->findBy('slug', $slug . '-' . $counter)) {
                $counter++;
            }
            $slug = $slug . '-' . $counter;
        }
        
        return $slug;
    }
} 