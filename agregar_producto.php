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

$categorias = [];
try {
    $res = $conexion->query("SELECT * FROM categorias");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $categorias[] = $row;
        }
    }
} catch (Throwable $e) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $cantidad = intval($_POST['cantidad'] ?? 0);
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;

    if (!empty($nombre) && $precio > 0) {
        $productoObj = new Producto();
        $exito = $productoObj->guardar($conexion, $nombre, $precio, $cantidad, $id_categoria);

        if ($exito) {
            header("Location: inventario.php");
            exit;
        } else {
            $errDetalle = $productoObj->getUltimoError();
            $mensaje = "Error al guardar en BD: " . ($errDetalle ? $errDetalle : "Revisa los campos obligatorios en MySQL.");
        }
    } else {
        $mensaje = "Por favor ingresa un nombre y un precio válido.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Tienda Minorista</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body {
            background: radial-gradient(circle at center, #121212 0%, #050505 80%);
            color: #d0d0d0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .card-form { background-color: #0d0d0d; border: 1px solid #1f1f1f; border-radius: 18px; padding: 40px; width: 100%; max-width: 440px; box-shadow: 0 30px 70px rgba(0,0,0,0.95); }
        h2 { color: #ffffff; font-size: 22px; margin-bottom: 24px; font-weight: 600; text-align: center; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 1.2px; color: #777777; margin-bottom: 8px; font-weight: 700; }
        input, select { width: 100%; padding: 13px 16px; background-color: #121212; border: 1px solid #202020; border-radius: 10px; color: #ffffff; font-size: 14px; outline: none; transition: border 0.2s; }
        input:focus, select:focus { border-color: #d4af37; background-color: #161616; }
        option { background-color: #121212; color: #ffffff; }
        .btn-submit { width: 100%; padding: 13px; background: linear-gradient(135deg, #ffe066 0%, #d4af37 100%); color: #0a0a0a; font-weight: 700; border: none; border-radius: 10px; font-size: 14px; cursor: pointer; margin-top: 10px; box-shadow: 0 0 15px rgba(212,175,55,0.2); }
        .btn-submit:hover { opacity: 0.9; }
        .btn-cancel { display: block; text-align: center; margin-top: 14px; color: #777777; text-decoration: none; font-size: 13px; font-weight: 600; }
        .btn-cancel:hover { color: #ffffff; }
        .alert-error { background: #2a1212; color: #ff6b6b; border: 1px solid #552222; padding: 10px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; word-break: break-word; }
    </style>
</head>
<body>

    <div class="card-form">
        <h2>📦 Registrar Producto</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <form action="agregar_producto.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre del Producto</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej. Chocolate Amargo 100g" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="precio">Precio ($)</label>
                <input type="number" step="0.01" id="precio" name="precio" placeholder="25.00" required>
            </div>

            <div class="form-group">
                <label for="cantidad">Stock / Unidades</label>
                <input type="number" id="cantidad" name="cantidad" placeholder="15" required>
            </div>

            <div class="form-group">
                <label for="id_categoria">Categoría (Opcional)</label>
                <select id="id_categoria" name="id_categoria">
                    <option value="">-- Sin Categoría --</option>
                    <?php foreach ($categorias as $cat):
                        $catId = $cat['id_categoria'] ?? $cat['id'] ?? '';
                    ?>
                        <option value="<?php echo $catId; ?>">
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-submit">Guardar en Base de Datos</button>
            <a href="inventario.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>

</body>
</html>
<?php $db->cerrarConexion(); ?>