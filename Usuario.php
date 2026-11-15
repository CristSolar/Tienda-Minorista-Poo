<?php
class Usuario {

    private $id_usuario;
    private $usuario;
    private $contrasena;
    private $rol;


    public function __construct($usuario = "", $contrasena = "") {
        $this->usuario = $usuario;
        $this->contrasena = $contrasena;
    }


    public function getUsuario() {
        return $this->usuario;
    }

    public function getRol() {
        return $this->rol;
    }

    public function setRol($rol) {
        $this->rol = $rol;
    }

    // Método para autenticar al usuario contra la BD
    public function autenticar($dbConexion) {
        $sql = "SELECT * FROM usuario WHERE usuario = ? AND contrasena = ?";
        $stmt = $dbConexion->prepare($sql);
        $stmt->bind_param("ss", $this->usuario, $this->contrasena);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $datos = $resultado->fetch_assoc();
            $this->id_usuario = $datos['id_usuario'];
            $this->rol = $datos['rol'];
            return true;
        }
        return false;
    }
}
?>