<?php

require_once __DIR__ . '/../config/database.php';

class Servicio {
    private $conn;
    private $table = 'servicios';

    public $id;
    public $usuario_id;
    public $fecha_servicio;
    public $hora_servicio;
    public $numero_personas;
    public $amenidad_id;
    public $descripcion_servicio;
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
                 (usuario_id, fecha_servicio, hora_servicio, numero_personas, amenidad_id, descripcion_servicio, notas) 
                 VALUES (:usuario_id, :fecha_servicio, :hora_servicio, :numero_personas, :amenidad_id, :descripcion_servicio, :notas)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':usuario_id', $this->usuario_id);
        $stmt->bindParam(':fecha_servicio', $this->fecha_servicio);
        $stmt->bindParam(':hora_servicio', $this->hora_servicio);
        $stmt->bindParam(':numero_personas', $this->numero_personas);
        $stmt->bindParam(':amenidad_id', $this->amenidad_id);
        $stmt->bindParam(':descripcion_servicio', $this->descripcion_servicio);
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
        $query = "SELECT s.*, u.nombre as cliente_nombre, a.nombre as amenidad_nombre, 
                         p.nombre as personal_nombre
                 FROM " . $this->table . " s
                 LEFT JOIN usuarios u ON s.usuario_id = u.id
                 LEFT JOIN amenidades a ON s.amenidad_id = a.id
                 LEFT JOIN usuarios p ON s.personal_asignado_id = p.id
                 ORDER BY s.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUsuario($usuario_id) {
        $query = "SELECT s.*, a.nombre as amenidad_nombre, p.nombre as personal_nombre
                 FROM " . $this->table . " s
                 LEFT JOIN amenidades a ON s.amenidad_id = a.id
                 LEFT JOIN usuarios p ON s.personal_asignado_id = p.id
                 WHERE s.usuario_id = :usuario_id
                 ORDER BY s.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $query = "SELECT s.*, u.nombre as cliente_nombre, a.nombre as amenidad_nombre, 
                         p.nombre as personal_nombre
                 FROM " . $this->table . " s
                 LEFT JOIN usuarios u ON s.usuario_id = u.id
                 LEFT JOIN amenidades a ON s.amenidad_id = a.id
                 LEFT JOIN usuarios p ON s.personal_asignado_id = p.id
                 WHERE s.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->usuario_id = $row['usuario_id'];
            $this->fecha_servicio = $row['fecha_servicio'];
            $this->hora_servicio = $row['hora_servicio'];
            $this->numero_personas = $row['numero_personas'];
            $this->amenidad_id = $row['amenidad_id'];
            $this->descripcion_servicio = $row['descripcion_servicio'];
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

class Actividad {
    private $conn;
    private $table = 'actividades';

    public $id;
    public $tipo_solicitud;
    public $solicitud_id;
    public $usuario_id;
    public $actividad;
    public $fecha_actividad;

    public function __construct() {
        $this->conn = getDB();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 (tipo_solicitud, solicitud_id, usuario_id, actividad) 
                 VALUES (:tipo_solicitud, :solicitud_id, :usuario_id, :actividad)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':tipo_solicitud', $this->tipo_solicitud);
        $stmt->bindParam(':solicitud_id', $this->solicitud_id);
        $stmt->bindParam(':usuario_id', $this->usuario_id);
        $stmt->bindParam(':actividad', $this->actividad);

        return $stmt->execute();
    }

    public function getBySolicitud($tipo_solicitud, $solicitud_id) {
        $query = "SELECT a.*, u.nombre as usuario_nombre, u.tipo_usuario
                 FROM " . $this->table . " a
                 LEFT JOIN usuarios u ON a.usuario_id = u.id
                 WHERE a.tipo_solicitud = :tipo_solicitud AND a.solicitud_id = :solicitud_id
                 ORDER BY a.fecha_actividad DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tipo_solicitud', $tipo_solicitud);
        $stmt->bindParam(':solicitud_id', $solicitud_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

class Amenidad {
    private $conn;
    private $table = 'amenidades';

    public function __construct() {
        $this->conn = getDB();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " WHERE activo = 1 ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}