<?php
/**
 * Simple installation test script
 * Run this to verify the system is working correctly
 */

// Test database connection
echo "Testing Sistema de Reservaciones...\n\n";

try {
    // Test 1: Include configuration
    echo "1. Testing configuration... ";
    require_once __DIR__ . '/config/database.php';
    echo "✓ OK\n";
    
    // Test 2: Database connection
    echo "2. Testing database connection... ";
    $db = getDB();
    if ($db) {
        echo "✓ OK\n";
    } else {
        echo "✗ FAILED\n";
        exit(1);
    }
    
    // Test 3: Test models
    echo "3. Testing models... ";
    require_once __DIR__ . '/models/Usuario.php';
    require_once __DIR__ . '/models/Reservacion.php';
    require_once __DIR__ . '/models/Producto.php';
    require_once __DIR__ . '/models/Servicio.php';
    echo "✓ OK\n";
    
    // Test 4: Test controllers
    echo "4. Testing controllers... ";
    require_once __DIR__ . '/controllers/AuthController.php';
    require_once __DIR__ . '/controllers/DashboardController.php';
    require_once __DIR__ . '/controllers/ReservacionController.php';
    require_once __DIR__ . '/controllers/CompraController.php';
    echo "✓ OK\n";
    
    // Test 5: Check database tables
    echo "5. Checking database tables... ";
    $tables = ['usuarios', 'reservaciones', 'productos', 'compras', 'servicios', 'amenidades', 'actividades'];
    foreach ($tables as $table) {
        $query = "SHOW TABLES LIKE '$table'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            echo "✗ FAILED - Table '$table' not found\n";
            echo "Please run: mysql -u username -p < db_schema.sql\n";
            exit(1);
        }
    }
    echo "✓ OK\n";
    
    // Test 6: Check sample data
    echo "6. Checking sample data... ";
    $query = "SELECT COUNT(*) as count FROM usuarios";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result['count'] < 3) {
        echo "✗ FAILED - Not enough sample users\n";
        echo "Please run: mysql -u username -p < db_schema.sql\n";
        exit(1);
    }
    echo "✓ OK\n";
    
    // Test 7: Check uploads directory
    echo "7. Checking uploads directory... ";
    if (!file_exists(__DIR__ . '/uploads')) {
        mkdir(__DIR__ . '/uploads', 0755, true);
    }
    if (!is_writable(__DIR__ . '/uploads')) {
        echo "✗ FAILED - Uploads directory not writable\n";
        echo "Please run: chmod 755 uploads/\n";
        exit(1);
    }
    echo "✓ OK\n";
    
    echo "\n🎉 All tests passed! Sistema de Reservaciones is ready to use.\n\n";
    echo "Sample users:\n";
    echo "- Admin: admin@reservaciones.com / password\n";
    echo "- Comercio: comercio@reservaciones.com / password\n";
    echo "- Cliente: cliente@reservaciones.com / password\n\n";
    echo "Access the system at: " . BASE_URL . "\n";
    
} catch (Exception $e) {
    echo "✗ FAILED - " . $e->getMessage() . "\n";
    exit(1);
}
?>