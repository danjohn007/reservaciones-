<?php
require_once __DIR__ . '/../config/database.php';

// Routing
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

// Include controllers
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/ReservacionController.php';
require_once __DIR__ . '/../controllers/CompraController.php';

try {
    switch ($page) {
        case 'login':
            $controller = new AuthController();
            $controller->login();
            break;
            
        case 'register':
            $controller = new AuthController();
            $controller->register();
            break;
            
        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;
            
        case 'dashboard':
            $controller = new DashboardController();
            switch ($type) {
                case 'admin':
                    $controller->admin();
                    break;
                case 'comercio':
                    $controller->comercio();
                    break;
                case 'cliente':
                default:
                    $controller->cliente();
                    break;
            }
            break;
            
        case 'form':
            switch ($type) {
                case 'reservacion':
                    $controller = new ReservacionController();
                    $controller->create();
                    break;
                case 'compra':
                    $controller = new CompraController();
                    $controller->create();
                    break;
                case 'servicio':
                    $controller = new ServicioController();
                    $controller->create();
                    break;
                default:
                    header('Location: ' . getUrl('index.php?page=dashboard&type=' . ($_SESSION['user_type'] ?? 'cliente')));
                    exit;
            }
            break;
            
        case 'reservacion':
            $controller = new ReservacionController();
            switch ($action) {
                case 'view':
                    $controller->view($id);
                    break;
                case 'upload':
                    $controller->uploadComprobante();
                    break;
                default:
                    $controller->create();
            }
            break;
            
        case 'compra':
            $controller = new CompraController();
            switch ($action) {
                case 'view':
                    $controller->view($id);
                    break;
                case 'upload':
                    $controller->uploadComprobante();
                    break;
                default:
                    $controller->create();
            }
            break;
            
        case 'servicio':
            $controller = new ServicioController();
            switch ($action) {
                case 'view':
                    $controller->view($id);
                    break;
                case 'upload':
                    $controller->uploadComprobante();
                    break;
                default:
                    $controller->create();
            }
            break;
            
        case 'update':
            $controller = new DashboardController();
            $controller->updateSolicitud();
            break;
            
        default:
            // Redirigir a login si no hay sesión, o al dashboard correspondiente
            if (isset($_SESSION['user_id'])) {
                header('Location: ' . getUrl('index.php?page=dashboard&type=' . $_SESSION['user_type']));
            } else {
                header('Location: ' . getUrl('index.php?page=login'));
            }
            exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Error del sistema: ' . $e->getMessage();
    if (isset($_SESSION['user_id'])) {
        header('Location: ' . getUrl('index.php?page=dashboard&type=' . $_SESSION['user_type']));
    } else {
        header('Location: ' . getUrl('index.php?page=login'));
    }
    exit;
}
?>