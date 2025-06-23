<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;

class CategoryController extends Controller {
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->categoryModel = new Category();
    }
    
    public function index() {
        $categories = $this->categoryModel->getAllWithCounts();
        
        return $this->render('categories/index', [
            'categories' => $categories
        ]);
    }
    
    public function show($id) {
        $page = $this->getQuery('page', 1);
        $category = $this->categoryModel->getWithProducts($id, $page);
        
        if (!$category) {
            $this->redirect('/categories');
        }
        
        return $this->render('categories/show', [
            'category' => $category
        ]);
    }
    
    public function create() {
        $this->requireAdmin();
        
        if ($this->isPost()) {
            $data = $this->getPost();
            
            $errors = $this->validate($data, [
                'name' => 'required|min:3|max:100',
                'description' => 'max:1000'
            ]);
            
            if (empty($errors)) {
                $this->categoryModel->create([
                    'name' => $data['name'],
                    'slug' => $this->categoryModel->createSlug($data['name']),
                    'description' => $data['description'] ?? null,
                    'parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null
                ]);
                
                $_SESSION['flash']['success'] = 'Category created successfully';
                $this->redirect('/admin/categories');
            }
            
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect('/admin/categories/create');
        }
        
        $categories = $this->categoryModel->findAll();
        
        return $this->render('admin/categories/create', [
            'categories' => $categories
        ]);
    }
    
    public function edit($id) {
        $this->requireAdmin();
        
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $this->redirect('/admin/categories');
        }
        
        if ($this->isPost()) {
            $data = $this->getPost();
            
            $errors = $this->validate($data, [
                'name' => 'required|min:3|max:100',
                'description' => 'max:1000'
            ]);
            
            if (empty($errors)) {
                $this->categoryModel->update($id, [
                    'name' => $data['name'],
                    'slug' => $this->categoryModel->createSlug($data['name']),
                    'description' => $data['description'] ?? null,
                    'parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null
                ]);
                
                $_SESSION['flash']['success'] = 'Category updated successfully';
                $this->redirect('/admin/categories');
            }
            
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect("/admin/categories/{$id}/edit");
        }
        
        $categories = $this->categoryModel->findAll();
        
        return $this->render('admin/categories/edit', [
            'category' => $category,
            'categories' => $categories
        ]);
    }
    
    public function delete($id) {
        $this->requireAdmin();
        
        $category = $this->categoryModel->find($id);
        
        if ($category) {
            // Check if category has products
            $sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
            $count = $this->categoryModel->db->fetch($sql, [$id])['count'];
            
            if ($count > 0) {
                $_SESSION['flash']['error'] = 'Cannot delete category with products';
            } else {
                $this->categoryModel->delete($id);
                $_SESSION['flash']['success'] = 'Category deleted successfully';
            }
        }
        
        $this->redirect('/admin/categories');
    }
} 