<?php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Por favor complete todos los campos';
                header('Location: /public/index.php?page=login');
                exit;
            }
            
            $usuario = new Usuario();
            if ($usuario->login($email, $password)) {
                $_SESSION['user_id'] = $usuario->id;
                $_SESSION['user_name'] = $usuario->nombre;
                $_SESSION['user_email'] = $usuario->email;
                $_SESSION['user_type'] = $usuario->tipo_usuario;
                
                // Redirigir según tipo de usuario
                switch ($usuario->tipo_usuario) {
                    case 'admin':
                        header('Location: /public/index.php?page=dashboard&type=admin');
                        break;
                    case 'comercio':
                        header('Location: /public/index.php?page=dashboard&type=comercio');
                        break;
                    case 'cliente':
                        header('Location: /public/index.php?page=dashboard&type=cliente');
                        break;
                    default:
                        header('Location: /public/index.php?page=login');
                }
                exit;
            } else {
                $_SESSION['error'] = 'Credenciales incorrectas';
                header('Location: /public/index.php?page=login');
                exit;
            }
        }
        
        // Mostrar formulario de login
        include __DIR__ . '/../views/auth/login.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $tipo_usuario = $_POST['tipo_usuario'];
            $telefono = trim($_POST['telefono']);
            $direccion = trim($_POST['direccion']);
            
            // Validaciones
            if (empty($nombre) || empty($email) || empty($password)) {
                $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
                header('Location: /public/index.php?page=register');
                exit;
            }
            
            if ($password !== $confirm_password) {
                $_SESSION['error'] = 'Las contraseñas no coinciden';
                header('Location: /public/index.php?page=register');
                exit;
            }
            
            if (!in_array($tipo_usuario, ['cliente', 'comercio'])) {
                $tipo_usuario = 'cliente'; // Por defecto cliente
            }
            
            $usuario = new Usuario();
            $usuario->nombre = $nombre;
            $usuario->email = $email;
            $usuario->password = $password;
            $usuario->tipo_usuario = $tipo_usuario;
            $usuario->telefono = $telefono;
            $usuario->direccion = $direccion;
            
            if ($usuario->register()) {
                $_SESSION['success'] = 'Registro exitoso. Puede iniciar sesión.';
                header('Location: /public/index.php?page=login');
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar usuario. El email podría estar en uso.';
                header('Location: /public/index.php?page=register');
                exit;
            }
        }
        
        // Mostrar formulario de registro
        include __DIR__ . '/../views/auth/register.php';
    }
    
    public function logout() {
        session_destroy();
        header('Location: /public/index.php?page=login');
        exit;
    }
    
    public static function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /public/index.php?page=login');
            exit;
        }
    }
    
    public static function requireRole($roles) {
        self::requireAuth();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        if (!in_array($_SESSION['user_type'], $roles)) {
            $_SESSION['error'] = 'No tiene permisos para acceder a esta página';
            header('Location: /public/index.php?page=dashboard&type=' . $_SESSION['user_type']);
            exit;
        }
    }
}