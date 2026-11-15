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
$listaProductos = [];
if (class_exists('Producto')) {
    $productoObj = new Producto();
    $listaProductos = $productoObj->obtenerTodos($db->getConexion());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Tienda Minorista</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }

        body {
            background: radial-gradient(circle at center, #1d1d1d 0%, #020202 85%);
            color: #d0d0d0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        .main-container {
            max-width: 1200px;
            width: 100%;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 25px;
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0d0d0d;
            border: 1px solid #1f1f1f;
            border-radius: 16px;
            padding: 24px 32px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.9);
        }

        .header-title-wrapper {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-icon {
            width: 36px;
            height: 36px;
            background: #161616;
            border: 1px solid #262626;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .header-icon svg {
            width: 18px;
            height: 18px;
            fill: #e0e0e0;
        }

        h1 { font-size: 20px; color: #ffffff; font-weight: 600; letter-spacing: 0.5px; }

        .btn-volver {
            padding: 10px 18px;
            background: #161616;
            color: #aaaaaa;
            text-decoration: none;
            border-radius: 10px;
            border: 1px solid #262626;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-volver:hover { color: #fff; border-color: #444; background: #202020; }

        .grid-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }

        .card-product {
            background: #0d0d0d;
            border: 1px solid #1f1f1f;
            border-radius: 16px;
            padding: 26px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.8);
            transition: transform 0.2s, border-color 0.2s;
        }
        .card-product:hover { border-color: #383838; transform: translateY(-3px); }

        .category-tag { font-size: 11px; text-transform: uppercase; color: #777777; letter-spacing: 1.2px; font-weight: 700; margin-bottom: 8px; }
        .product-title { font-size: 18px; color: #ffffff; font-weight: 600; margin-bottom: 6px; }
        .code-tag { font-size: 12px; color: #555555; margin-bottom: 14px; font-family: monospace; }

        /* Precios limpios en blanco puro profesional */
        .price { font-size: 24px; color: #ffffff; font-weight: 700; margin-bottom: 16px; }

        .stock-badge { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; }

        /* Colores de estado atenuados y elegantes */
        .stock-ok { background: rgba(61, 168, 96, 0.12); color: #3da860; border: 1px solid rgba(61, 168, 96, 0.25); }
        .stock-low { background: rgba(179, 71, 71, 0.12); color: #b34747; border: 1px solid rgba(179, 71, 71, 0.25); }

        .page-footer {
            text-align: center;
            padding: 24px;
            font-size: 11px;
            color: #444444;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-top: 1px solid rgba(255, 255, 255, 0.02);
            margin-top: auto;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="header">
            <div class="header-title-wrapper">
                <div class="header-icon">
                    <!-- Icono SVG de lista / catálogo minimalista en B/N -->
                    <svg viewBox="0 0 24 24"><path d="M19 3H5C3.89 3 3 3.89 3 5V19C3 20.1 3.89 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.89 20.1 3 19 3M19 5V19H5V5H19M7 7H17V9H7V7M7 11H17V13H7V11M7 15H14V17H7V15Z"/></svg>
                </div>
                <h1>Catálogo de Productos Disponibles</h1>
            </div>
            <a href="login.php" class="btn-volver">← Panel Principal</a>
        </div>

        <div class="grid-container">
            <?php foreach ($listaProductos as $p):
                $stockVal = $p['cantidad_disponible'] ?? $p['stock'] ?? $p['cantidad'] ?? $p['existencia'] ?? 0;
                $codigoVal = $p['codigo'] ?? '';
            ?>
            <div class="card-product">
                <div class="category-tag"><?php echo htmlspecialchars($p['categoria'] ?? 'General'); ?></div>
                <div class="product-title"><?php echo htmlspecialchars($p['nombre'] ?? 'Producto'); ?></div>
                <?php if (!empty($codigoVal)): ?>
                    <div class="code-tag">Cód: <?php echo htmlspecialchars($codigoVal); ?></div>
                <?php endif; ?>
                <div class="price">$<?php echo number_format(floatval($p['precio'] ?? 0), 2); ?></div>
                <?php if ($stockVal > 5): ?>
                    <span class="stock-badge stock-ok">Disponible: <?php echo $stockVal; ?> und.</span>
                <?php elseif ($stockVal > 0): ?>
                    <span class="stock-badge stock-low">¡Últimas unidades! (<?php echo $stockVal; ?> und.)</span>
                <?php else: ?>
                    <span class="stock-badge stock-low" style="background: rgba(255,255,255,0.04); color:#777777; border-color:#262626;">Agotado</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <?php if (empty($listaProductos)): ?>
                <p style="color: #777777; grid-column: 1/-1; text-align: center; padding: 40px;">No hay productos registrados en la base de datos.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="page-footer">Sistema de Gestión Comercial v1.0 • Universidad Autónoma de Chiapas (UNACH)</div>

</body>
</html>
<?php $db->cerrarConexion(); ?>