<?php

require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $conn;
    private $table = 'usuarios';

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $tipo_usuario;
    public $telefono;
    public $direccion;
    public $fecha_registro;
    public $activo;

    public function __construct() {
        $this->conn = getDB();
    }

    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email AND activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->nombre = $row['nombre'];
                $this->email = $row['email'];
                $this->tipo_usuario = $row['tipo_usuario'];
                $this->telefono = $row['telefono'];
                $this->direccion = $row['direccion'];
                $this->fecha_registro = $row['fecha_registro'];
                $this->activo = $row['activo'];
                return true;
            }
        }
        return false;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                 (nombre, email, password, tipo_usuario, telefono, direccion) 
                 VALUES (:nombre, :email, :password, :tipo_usuario, :telefono, :direccion)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':tipo_usuario', $this->tipo_usuario);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':direccion', $this->direccion);

        return $stmt->execute();
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
            $this->email = $row['email'];
            $this->tipo_usuario = $row['tipo_usuario'];
            $this->telefono = $row['telefono'];
            $this->direccion = $row['direccion'];
            $this->fecha_registro = $row['fecha_registro'];
            $this->activo = $row['activo'];
            return true;
        }
        return false;
    }

    public function getAllByType($tipo) {
        $query = "SELECT * FROM " . $this->table . " WHERE tipo_usuario = :tipo AND activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}