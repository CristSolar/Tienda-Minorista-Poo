<?php
class Conexion {
    private $host = "localhost";
    private $usuario = "root";
    private $password = "";
    private $base_datos = "tienda_minorista_poo";
    private $conexion;

    public function __construct() {
        try {
            $this->conexion = new mysqli($this->host, $this->usuario, $this->password, $this->base_datos);
            $this->conexion->set_charset("utf8mb4");

            if ($this->conexion->connect_error) {
                die("Error de conexión: " . $this->conexion->connect_error);
            }
        } catch (Throwable $e) {
            die("Error en servidor: " . $e->getMessage());
        }
    }

    public function getConexion() {
        return $this->conexion;
    }

    public function cerrarConexion() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
?>