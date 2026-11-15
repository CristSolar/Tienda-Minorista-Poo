<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'conexion.php';
if (file_exists('Producto.php')) {
    require_once 'Producto.php';
} elseif (file_exists('producto.php')) {
    require_once 'producto.php';
}

$db = new Conexion();
$conexion = $db->getConexion();

$mensaje = "";
$tipoMensaje = "";

$productoObj = new Producto();
$listaProductos = $productoObj->obtenerTodos($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_prod = intval($_POST['id_producto'] ?? 0);
    $cant_vender = intval($_POST['cantidad_vender'] ?? 0);

    if ($id_prod > 0 && $cant_vender > 0) {
        $prodEncontrado = null;
        foreach ($listaProductos as $p) {
            $pId = $p['id_producto'] ?? $p['id'] ?? 0;
            if ($pId == $id_prod) {
                $prodEncontrado = $p;
                break;
            }
        }

        if ($prodEncontrado) {
            $stockActual = intval($prodEncontrado['stock'] ?? $prodEncontrado['cantidad'] ?? $prodEncontrado['existencia'] ?? $prodEncontrado['existencias'] ?? 0);
            $precioUnitario = floatval($prodEncontrado['precio'] ?? 0);
            $totalVenta = $precioUnitario * $cant_vender;

            if ($stockActual >= $cant_vender) {
                $nuevoStock = $stockActual - $cant_vender;

                // Iniciar transacción para asegurar integridad
                $conexion->begin_transaction();

                try {
                    // 1. Registrar la venta en la tabla ventas
                    $sqlVenta = "INSERT INTO ventas (id_producto, cantidad, total) VALUES (?, ?, ?)";
                    $stmtVenta = $conexion->prepare($sqlVenta);
                    $stmtVenta->bind_param("iid", $id_prod, $cant_vender, $totalVenta);
                    $stmtVenta->execute();

                    // 2. Actualizar el stock en la tabla productos
                    $actualizado = false;
                    $columnas = ['cantidad_disponible', 'cantidad', 'stock', 'existencia', 'existencias'];
                    foreach ($columnas as $col) {
                        $sqlProd = "UPDATE productos SET $col = ? WHERE id_producto = ?";
                        $stmtProd = $conexion->prepare($sqlProd);
                        if (!$stmtProd) {
                            $sqlProd = "UPDATE productos SET $col = ? WHERE id = ?";
                            $stmtProd = $conexion->prepare($sqlProd);
                        }
                        if ($stmtProd) {
                            $stmtProd->bind_param("ii", $nuevoStock, $id_prod);
                            if ($stmtProd->execute()) {
                                $actualizado = true;
                                break;
                            }
                        }
                    }

                    if ($actualizado) {
                        $conexion->commit();
                        $mensaje = "¡Venta registrada con éxito! Total cobrado: $" . number_format($totalVenta, 2);
                        $tipoMensaje = "success";
                    } else {
                        throw new Exception("No se pudo actualizar el stock del producto.");
                    }
                } catch (Throwable $e) {
                    $conexion->rollback();
                    $mensaje = "Error al procesar la venta: " . $e->getMessage();
                    $tipoMensaje = "error";
                }

                $listaProductos = $productoObj->obtenerTodos($conexion);
            } else {
                $mensaje = "Stock insuficiente. Solo hay $stockActual unidades disponibles.";
                $tipoMensaje = "error";
            }
        } else {
            $mensaje = "El producto seleccionado no existe.";
            $tipoMensaje = "error";
        }
    } else {
        $mensaje = "Por favor selecciona un producto y una cantidad válida.";
        $tipoMensaje = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Venta - Tienda Minorista</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body {
            background: radial-gradient(circle at center, #1d1d1d 0%, #020202 85%);
            color: #d0d0d0; min-height: 100vh; display: flex; flex-direction: column;
            justify-content: center; align-items: center; padding: 20px; position: relative; overflow-x: hidden;
        }
        .card-form {
            background-color: #0d0d0d; border: 1px solid #1f1f1f; border-radius: 18px;
            padding: 40px; width: 100%; max-width: 440px; box-shadow: 0 30px 70px rgba(0,0,0,0.95); position: relative; z-index: 10;
        }
        .form-header { text-align: center; margin-bottom: 24px; }
        .brand-icon {
            width: 42px; height: 42px; background: #161616; border: 1px solid #262626;
            border-radius: 12px; margin: 0 auto 14px auto; display: flex; justify-content: center; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        .brand-icon svg { width: 20px; height: 20px; fill: #e0e0e0; }
        h2 { color: #ffffff; font-size: 20px; font-weight: 600; letter-spacing: 0.5px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 1.2px; color: #777777; margin-bottom: 8px; font-weight: 700; }
        input, select {
            width: 100%; padding: 13px 16px; background-color: #121212; border: 1px solid #202020;
            border-radius: 10px; color: #ffffff; font-size: 14px; outline: none; transition: border 0.2s;
        }
        input:focus, select:focus { border-color: #555555; background-color: #161616; }
        option { background-color: #121212; color: #ffffff; }
        .btn-submit {
            width: 100%; padding: 13px; background: linear-gradient(135deg, #e5e5e5 0%, #a3a3a3 100%);
            color: #080808; font-weight: 700; border: none; border-radius: 10px; font-size: 14px; cursor: pointer;
            margin-top: 10px; box-shadow: 0 0 20px rgba(255, 255, 255, 0.25); transition: all 0.2s;
        }
        .btn-submit:hover { opacity: 0.95; transform: translateY(-1px); box-shadow: 0 0 25px rgba(255, 255, 255, 0.4); }
        .btn-cancel { display: block; text-align: center; margin-top: 16px; color: #777777; text-decoration: none; font-size: 13px; font-weight: 600; transition: color 0.2s; }
        .btn-cancel:hover { color: #ffffff; }
        .alert-error { background: #2a1212; color: #ff6b6b; border: 1px solid #552222; padding: 10px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; }
        .alert-success { background: #122a18; color: #3da860; border: 1px solid #225533; padding: 10px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; }
        .page-footer { position: absolute; bottom: 20px; font-size: 11px; color: #444444; letter-spacing: 1px; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="card-form">
        <div class="form-header">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24"><path d="M17 18C15.89 18 15 18.89 15 20C15 21.11 15.89 22 17 22C18.11 22 19 21.11 19 20C19 18.89 18.11 18 17 18M1 2V4H3L6.6 11.59L5.25 14.04C5.09 14.32 5 14.65 5 15C5 16.1 5.9 17 7 17H19V15H7.42C7.29 15 7.18 14.89 7.18 14.76L7.2 14.63L8.1 13H15.55C16.3 13 16.96 12.58 17.3 11.97L21.88 3.5C21.95 3.34 22 3.17 22 3C22 2.45 21.55 2 21 2H4.21L3.27 2H1M7 18C5.89 18 5 18.89 5 20C5 21.11 5.89 22 7 22C8.11 22 9 21.11 9 20C9 18.89 8.11 18 7 18Z"/></svg>
            </div>
            <h2>Registrar Nueva Venta</h2>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="<?php echo ($tipoMensaje === 'success') ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form action="venta.php" method="POST">
            <div class="form-group">
                <label for="id_producto">Producto a Vender</label>
                <select id="id_producto" name="id_producto" required>
                    <option value="">-- Seleccionar Producto --</option>
                    <?php foreach ($listaProductos as $p):
                        $pId = $p['id_producto'] ?? $p['id'] ?? '';
                        $pStock = $p['stock'] ?? $p['cantidad'] ?? $p['existencia'] ?? $p['existencias'] ?? 0;
                        $pPrecio = floatval($p['precio'] ?? 0);
                    ?>
                        <option value="<?php echo $pId; ?>">
                            <?php echo htmlspecialchars($p['nombre']); ?> - $<?php echo number_format($pPrecio, 2); ?> (Disponibles: <?php echo $pStock; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="cantidad_vender">Cantidad Unidades</label>
                <input type="number" id="cantidad_vender" name="cantidad_vender" min="1" value="1" required>
            </div>

            <button type="submit" class="btn-submit">Procesar y Cobrar Venta</button>
            <a href="login.php" class="btn-cancel">Volver al Panel</a>
        </form>
    </div>

    <div class="page-footer">Sistema de Gestión Comercial v1.0 • Universidad Autónoma de Chiapas (UNACH)</div>

</body>
</html>
<?php $db->cerrarConexion(); ?>