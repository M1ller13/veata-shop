<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    public function index() {
        $page = $this->getQuery('page', 1);
        $search = $this->getQuery('search', '');
        $category = $this->getQuery('category', '');
        
        $products = $search 
            ? $this->productModel->search($search, $page)
            : $this->productModel->getAllWithCategories($page);
            
        $categories = $this->categoryModel->findAll();
        
        return $this->render('products/index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'category' => $category
        ]);
    }
    
    public function show($id) {
        $product = $this->productModel->getWithCategory($id);
        
        if (!$product) {
            $this->redirect('/products');
        }
        
        return $this->render('products/show', [
            'product' => $product
        ]);
    }
    
    public function create() {
        $this->requireAdmin();
        
        if ($this->isPost()) {
            $data = $this->getPost();
            $file = $this->getFile('image');
            
            $errors = $this->validate($data, [
                'name' => 'required|min:3|max:255',
                'description' => 'required|min:10',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'category_id' => 'required|integer'
            ]);
            
            if (empty($errors)) {
                $imagePath = null;
                if ($file && $file['error'] === UPLOAD_ERR_OK) {
                    $imagePath = $this->handleImageUpload($file);
                }
                
                $productId = $this->productModel->create([
                    'name' => $data['name'],
                    'slug' => $this->createSlug($data['name']),
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'stock' => $data['stock'],
                    'category_id' => $data['category_id'],
                    'image' => $imagePath
                ]);
                
                $_SESSION['flash']['success'] = 'Product created successfully';
                $this->redirect('/admin/products');
            }
            
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect('/admin/products/create');
        }
        
        $categories = $this->categoryModel->findAll();
        
        return $this->render('admin/products/create', [
            'categories' => $categories
        ]);
    }
    
    public function edit($id) {
        $this->requireAdmin();
        
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->redirect('/admin/products');
        }
        
        if ($this->isPost()) {
            $data = $this->getPost();
            $file = $this->getFile('image');
            
            $errors = $this->validate($data, [
                'name' => 'required|min:3|max:255',
                'description' => 'required|min:10',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'category_id' => 'required|integer'
            ]);
            
            if (empty($errors)) {
                $imagePath = $product['image'];
                if ($file && $file['error'] === UPLOAD_ERR_OK) {
                    $imagePath = $this->handleImageUpload($file);
                    if ($product['image']) {
                        $this->deleteImage($product['image']);
                    }
                }
                
                $this->productModel->update($id, [
                    'name' => $data['name'],
                    'slug' => $this->createSlug($data['name']),
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'stock' => $data['stock'],
                    'category_id' => $data['category_id'],
                    'image' => $imagePath
                ]);
                
                $_SESSION['flash']['success'] = 'Product updated successfully';
                $this->redirect('/admin/products');
            }
            
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect("/admin/products/{$id}/edit");
        }
        
        $categories = $this->categoryModel->findAll();
        
        return $this->render('admin/products/edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }
    
    public function delete($id) {
        $this->requireAdmin();
        
        $product = $this->productModel->find($id);
        
        if ($product) {
            if ($product['image']) {
                $this->deleteImage($product['image']);
            }
            
            $this->productModel->delete($id);
            $_SESSION['flash']['success'] = 'Product deleted successfully';
        }
        
        $this->redirect('/admin/products');
    }
    
    private function handleImageUpload($file) {
        $config = require __DIR__ . '/../../config/config.php';
        $uploadConfig = $config['upload'];
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $uploadConfig['allowed_types'])) {
            throw new \Exception('Invalid file type');
        }
        
        if ($file['size'] > $uploadConfig['max_size']) {
            throw new \Exception('File too large');
        }
        
        $filename = uniqid() . '.' . $extension;
        $path = $uploadConfig['path'] . '/' . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            throw new \Exception('Failed to upload file');
        }
        
        return $filename;
    }
    
    private function deleteImage($filename) {
        $config = require __DIR__ . '/../../config/config.php';
        $path = $config['upload']['path'] . '/' . $filename;
        
        if (file_exists($path)) {
            unlink($path);
        }
    }
    
    private function createSlug($name) {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
} 