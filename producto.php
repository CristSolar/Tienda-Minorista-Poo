<?php
class Producto {
    private $ultimoError = "";

    public function getUltimoError() {
        return $this->ultimoError;
    }

    public function obtenerTodos($dbConexion) {
        $productos = [];
        try {
            $sql = "SELECT p.*, c.nombre AS categoria
                    FROM productos p
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria";
            $resultado = $dbConexion->query($sql);
        } catch (Throwable $e) {
            try {
                $sql = "SELECT * FROM productos";
                $resultado = $dbConexion->query($sql);
            } catch (Throwable $e2) {
                return $productos;
            }
        }

        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                if (!isset($fila['stock']) && isset($fila['cantidad_disponible'])) {
                    $fila['stock'] = $fila['cantidad_disponible'];
                    $fila['cantidad'] = $fila['cantidad_disponible'];
                }
                $productos[] = $fila;
            }
        }
        return $productos;
    }

    public function guardar($dbConexion, $nombre, $precio, $cantidad, $id_categoria = null) {
        $nom = $dbConexion->real_escape_string(trim($nombre));
        $prec = floatval($precio);
        $cant = intval($cantidad);
        $id_cat = (!empty($id_categoria) && intval($id_categoria) > 0) ? intval($id_categoria) : null;

        $valCodigo = "PRD-" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

        if ($id_cat !== null) {
            $sql = "INSERT INTO productos (codigo, nombre, id_categoria, precio, cantidad_disponible, existencia_minima, fecha_registro)
                    VALUES ('$valCodigo', '$nom', $id_cat, $prec, $cant, 5, NOW())";
        } else {
            $sql = "INSERT INTO productos (codigo, nombre, precio, cantidad_disponible, existencia_minima, fecha_registro)
                    VALUES ('$valCodigo', '$nom', $prec, $cant, 5, NOW())";
        }

        try {
            if ($dbConexion->query($sql)) {
                return true;
            }
        } catch (Throwable $e) {
            $this->ultimoError = $e->getMessage();
        }

        if (empty($this->ultimoError)) {
            $this->ultimoError = $dbConexion->error;
        }

        return false;
    }
}
?>