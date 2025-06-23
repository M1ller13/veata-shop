<?php

namespace App\Core;

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    public function findAll($conditions = '', $params = [], $orderBy = '') {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function create($data) {
        return $this->db->insert($this->table, $data);
    }
    
    public function update($id, $data) {
        return $this->db->update(
            $this->table,
            $data,
            "{$this->primaryKey} = ?",
            [$id]
        );
    }
    
    public function delete($id) {
        return $this->db->delete(
            $this->table,
            "{$this->primaryKey} = ?",
            [$id]
        );
    }
    
    public function paginate($page = 1, $perPage = 10, $conditions = '', $params = [], $orderBy = '') {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $items = $this->db->fetchAll($sql, $params);
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($conditions) {
            $countSql .= " WHERE {$conditions}";
        }
        $total = $this->db->fetch($countSql, $params)['total'];
        
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    public function findBy($field, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = ?";
        return $this->db->fetch($sql, [$value]);
    }
    
    public function findAllBy($field, $value, $orderBy = '') {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = ?";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->fetchAll($sql, [$value]);
    }
} 