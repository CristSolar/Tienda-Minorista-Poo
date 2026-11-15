<?php
require_once 'conexion.php';

class Proveedor {
    private $conexion;
    private $id;
    private $nombre_empresa;
    private $contacto;
    private $telefono;
    private $email;
    private $direccion;

    public function __construct() {
        $db = new Conectar();
        $this->conexion = $db->conexion();
    }

    // Método para obtener todos los proveedores (ideal para tablas o selectores)
    public function obtenerProveedores() {
        $sql = "SELECT * FROM proveedores";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Método para registrar un nuevo proveedor
    public function insertarProveedor($nombre_empresa, $contacto, $telefono, $email, $direccion) {
        $stmt = $this->conexion->prepare("INSERT INTO proveedores (nombre_empresa, contacto, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre_empresa, $contacto, $telefono, $email, $direccion);
        return $stmt->execute();
    }
}
?>