<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function login() {
        if ($this->isAuthenticated()) {
            $this->redirect('/');
        }
        
        if ($this->isPost()) {
            $email = $this->getPost('email');
            $password = $this->getPost('password');
            
            $errors = $this->validate([
                'email' => $email,
                'password' => $password
            ], [
                'email' => 'required|email',
                'password' => 'required'
            ]);
            
            if (empty($errors)) {
                $user = $this->userModel->authenticate($email, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    $this->redirect('/');
                } else {
                    $errors['auth'] = 'Invalid email or password';
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = ['email' => $email];
                $this->redirect('/login');
            }
        }
        
        return $this->render('auth/login');
    }
    
    public function register() {
        if ($this->isAuthenticated()) {
            $this->redirect('/');
        }
        
        if ($this->isPost()) {
            $data = $this->getPost();
            
            $errors = $this->validate($data, [
                'name' => 'required|min:3|max:100',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'password_confirmation' => 'required|same:password'
            ]);
            
            if (empty($errors)) {
                $result = $this->userModel->register([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password']
                ]);
                
                if ($result['success']) {
                    $_SESSION['flash']['success'] = 'Registration successful. Please login.';
                    $this->redirect('/login');
                } else {
                    $errors = array_merge($errors, $result['errors']);
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                $this->redirect('/register');
            }
        }
        
        return $this->render('auth/register');
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
    
    public function profile() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $orders = $this->userModel->getOrders($user['id']);
        
        return $this->render('auth/profile', [
            'user' => $user,
            'orders' => $orders
        ]);
    }
    
    public function updateProfile() {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $data = $this->getPost();
            $user = $this->getCurrentUser();
            
            $errors = $this->validate($data, [
                'name' => 'required|min:3|max:100',
                'email' => 'required|email'
            ]);
            
            if (!empty($data['password'])) {
                $errors = array_merge($errors, $this->validate($data, [
                    'password' => 'min:6',
                    'password_confirmation' => 'same:password'
                ]));
            }
            
            if (empty($errors)) {
                $result = $this->userModel->updateProfile($user['id'], [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'] ?? null
                ]);
                
                if ($result['success']) {
                    $_SESSION['flash']['success'] = 'Profile updated successfully';
                    $this->redirect('/profile');
                } else {
                    $errors = array_merge($errors, $result['errors']);
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                $this->redirect('/profile');
            }
        }
        
        $this->redirect('/profile');
    }
} 