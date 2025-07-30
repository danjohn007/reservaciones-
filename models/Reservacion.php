<?php

require_once __DIR__ . '/../config/database.php';

class Reservacion {
    private $conn;
    private $table = 'reservaciones';

    public $id;
    public $usuario_id;
    public $fecha_reservacion;
    public $hora_reservacion;
    public $numero_personas;
    public $amenidad_id;
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
                 (usuario_id, fecha_reservacion, hora_reservacion, numero_personas, amenidad_id, notas) 
                 VALUES (:usuario_id, :fecha_reservacion, :hora_reservacion, :numero_personas, :amenidad_id, :notas)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':usuario_id', $this->usuario_id);
        $stmt->bindParam(':fecha_reservacion', $this->fecha_reservacion);
        $stmt->bindParam(':hora_reservacion', $this->hora_reservacion);
        $stmt->bindParam(':numero_personas', $this->numero_personas);
        $stmt->bindParam(':amenidad_id', $this->amenidad_id);
        $stmt->bindParam(':notas', $this->notas);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                 SET estado = :estado, costo_total = :costo_total, 
                     personal_asignado_id = :personal_asignado_id, notas = :notas
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':costo_total', $this->costo_total);
        $stmt->bindParam(':personal_asignado_id', $this->personal_asignado_id);
        $stmt->bindParam(':notas', $this->notas);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT r.*, u.nombre as cliente_nombre, a.nombre as amenidad_nombre, 
                         p.nombre as personal_nombre
                 FROM " . $this->table . " r
                 LEFT JOIN usuarios u ON r.usuario_id = u.id
                 LEFT JOIN amenidades a ON r.amenidad_id = a.id
                 LEFT JOIN usuarios p ON r.personal_asignado_id = p.id
                 ORDER BY r.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUsuario($usuario_id) {
        $query = "SELECT r.*, a.nombre as amenidad_nombre, p.nombre as personal_nombre
                 FROM " . $this->table . " r
                 LEFT JOIN amenidades a ON r.amenidad_id = a.id
                 LEFT JOIN usuarios p ON r.personal_asignado_id = p.id
                 WHERE r.usuario_id = :usuario_id
                 ORDER BY r.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $query = "SELECT r.*, u.nombre as cliente_nombre, a.nombre as amenidad_nombre, 
                         p.nombre as personal_nombre
                 FROM " . $this->table . " r
                 LEFT JOIN usuarios u ON r.usuario_id = u.id
                 LEFT JOIN amenidades a ON r.amenidad_id = a.id
                 LEFT JOIN usuarios p ON r.personal_asignado_id = p.id
                 WHERE r.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->usuario_id = $row['usuario_id'];
            $this->fecha_reservacion = $row['fecha_reservacion'];
            $this->hora_reservacion = $row['hora_reservacion'];
            $this->numero_personas = $row['numero_personas'];
            $this->amenidad_id = $row['amenidad_id'];
            $this->estado = $row['estado'];
            $this->costo_total = $row['costo_total'];
            $this->personal_asignado_id = $row['personal_asignado_id'];
            $this->notas = $row['notas'];
            $this->fecha_creacion = $row['fecha_creacion'];
            return $row;
        }
        return false;
    }

    public function getEstadisticas() {
        $query = "SELECT estado, COUNT(*) as total FROM " . $this->table . " GROUP BY estado";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}