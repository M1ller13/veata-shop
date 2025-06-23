<?php

namespace App\Models;

use App\Core\Model;

class Order extends Model {
    protected $table = 'orders';
    
    public function createFromCart($userId, $data) {
        $cartModel = new Cart();
        $items = $cartModel->getItems($userId);
        
        if (empty($items)) {
            return false;
        }
        
        // Start transaction
        $this->db->getConnection()->beginTransaction();
        
        try {
            // Create order
            $orderId = $this->create([
                'user_id' => $userId,
                'total_amount' => $cartModel->getTotal($userId),
                'status' => 'pending',
                'shipping_address' => $data['shipping_address'],
                'payment_method' => $data['payment_method']
            ]);
            
            // Create order items and update stock
            $productModel = new Product();
            foreach ($items as $item) {
                // Create order item
                $this->db->insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
                
                // Update product stock
                $productModel->updateStock($item['product_id'], $item['quantity']);
            }
            
            // Clear cart
            $cartModel->clearCart($userId);
            
            // Commit transaction
            $this->db->getConnection()->commit();
            
            return $orderId;
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->getConnection()->rollBack();
            throw $e;
        }
    }
    
    public function getWithItems($id) {
        $order = $this->find($id);
        
        if ($order) {
            $sql = "SELECT oi.*, p.name as product_name, p.image as product_image 
                    FROM order_items oi 
                    JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = ?";
            
            $order['items'] = $this->db->fetchAll($sql, [$id]);
        }
        
        return $order;
    }
    
    public function updateStatus($id, $status) {
        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }
        
        return $this->update($id, ['status' => $status]);
    }
    
    public function getByUser($userId, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT o.*, 
                       COUNT(oi.id) as item_count,
                       SUM(oi.quantity) as total_items
                FROM {$this->table} o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT ? OFFSET ?";
        
        $orders = $this->db->fetchAll($sql, [$userId, $perPage, $offset]);
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = ?";
        $total = $this->db->fetch($countSql, [$userId])['total'];
        
        return [
            'items' => $orders,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    public function getAll($page = 1, $perPage = 10, $status = null) {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';
        
        if ($status) {
            $where = 'WHERE status = ?';
            $params[] = $status;
        }
        
        $sql = "SELECT o.*, 
                       u.name as user_name,
                       COUNT(oi.id) as item_count,
                       SUM(oi.quantity) as total_items
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                {$where}
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $orders = $this->db->fetchAll($sql, $params);
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($status) {
            $countSql .= " WHERE status = ?";
        }
        $total = $this->db->fetch($countSql, $status ? [$status] : [])['total'];
        
        return [
            'items' => $orders,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
} 