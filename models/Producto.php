<?php

require_once __DIR__ . '/../config/database.php';

class Producto {
    private $conn;
    private $table = 'productos';

    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $stock;
    public $activo;
    public $fecha_creacion;

    public function __construct() {
        $this->conn = getDB();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " WHERE activo = 1 ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->precio = $row['precio'];
            $this->stock = $row['stock'];
            $this->activo = $row['activo'];
            $this->fecha_creacion = $row['fecha_creacion'];
            return true;
        }
        return false;
    }

    public function updateStock($cantidad) {
        $query = "UPDATE " . $this->table . " SET stock = stock - :cantidad WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}

class Compra {
    private $conn;
    private $table = 'compras';

    public $id;
    public $usuario_id;
    public $tipo_entrega;
    public $direccion_entrega;
    public $fecha_entrega;
    public $hora_entrega;
    public $estado;
    public $costo_total;
    public $personal_asignado_id;
    public $notas;
    public $fecha_creacion;

    public function __construct() {
        $this->conn = getDB();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 (usuario_id, tipo_entrega, direccion_entrega, fecha_entrega, hora_entrega, notas) 
                 VALUES (:usuario_id, :tipo_entrega, :direccion_entrega, :fecha_entrega, :hora_entrega, :notas)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':usuario_id', $this->usuario_id);
        $stmt->bindParam(':tipo_entrega', $this->tipo_entrega);
        $stmt->bindParam(':direccion_entrega', $this->direccion_entrega);
        $stmt->bindParam(':fecha_entrega', $this->fecha_entrega);
        $stmt->bindParam(':hora_entrega', $this->hora_entrega);
        $stmt->bindParam(':notas', $this->notas);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function addDetalle($producto_id, $cantidad, $precio_unitario) {
        $query = "INSERT INTO compra_detalles (compra_id, producto_id, cantidad, precio_unitario) 
                 VALUES (:compra_id, :producto_id, :cantidad, :precio_unitario)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':compra_id', $this->id);
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':precio_unitario', $precio_unitario);

        return $stmt->execute();
    }

    public function updateTotal() {
        $query = "UPDATE " . $this->table . " 
                 SET costo_total = (
                     SELECT SUM(cantidad * precio_unitario) 
                     FROM compra_detalles 
                     WHERE compra_id = :id
                 ) 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                 SET estado = :estado, personal_asignado_id = :personal_asignado_id, notas = :notas
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':personal_asignado_id', $this->personal_asignado_id);
        $stmt->bindParam(':notas', $this->notas);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT c.*, u.nombre as cliente_nombre, p.nombre as personal_nombre
                 FROM " . $this->table . " c
                 LEFT JOIN usuarios u ON c.usuario_id = u.id
                 LEFT JOIN usuarios p ON c.personal_asignado_id = p.id
                 ORDER BY c.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUsuario($usuario_id) {
        $query = "SELECT c.*, p.nombre as personal_nombre
                 FROM " . $this->table . " c
                 LEFT JOIN usuarios p ON c.personal_asignado_id = p.id
                 WHERE c.usuario_id = :usuario_id
                 ORDER BY c.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDetalles($compra_id) {
        $query = "SELECT cd.*, p.nombre as producto_nombre
                 FROM compra_detalles cd
                 LEFT JOIN productos p ON cd.producto_id = p.id
                 WHERE cd.compra_id = :compra_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':compra_id', $compra_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $query = "SELECT c.*, u.nombre as cliente_nombre, p.nombre as personal_nombre
                 FROM " . $this->table . " c
                 LEFT JOIN usuarios u ON c.usuario_id = u.id
                 LEFT JOIN usuarios p ON c.personal_asignado_id = p.id
                 WHERE c.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->usuario_id = $row['usuario_id'];
            $this->tipo_entrega = $row['tipo_entrega'];
            $this->direccion_entrega = $row['direccion_entrega'];
            $this->fecha_entrega = $row['fecha_entrega'];
            $this->hora_entrega = $row['hora_entrega'];
            $this->estado = $row['estado'];
            $this->costo_total = $row['costo_total'];
            $this->personal_asignado_id = $row['personal_asignado_id'];
            $this->notas = $row['notas'];
            $this->fecha_creacion = $row['fecha_creacion'];
            return $row;
        }
        return false;
    }
}