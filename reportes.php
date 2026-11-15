<?php
// Activar reporte de errores para evitar pantallas en blanco
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'conexion.php';

// Cargar la clase Producto con tolerancia a mayúsculas/minúsculas
if (file_exists('Producto.php')) {
    require_once 'Producto.php';
} elseif (file_exists('producto.php')) {
    require_once 'producto.php';
}

$listaProductos = [];
$totalProductos = 0;
$valorTotalInventario = 0;
$alertasStock = 0;

try {
    $db = new Conexion();
    if (class_exists('Producto')) {
        $productoObj = new Producto();
        $listaProductos = $productoObj->obtenerTodos($db->getConexion());
    }

    $totalProductos = count($listaProductos);

    foreach ($listaProductos as $p) {
        $cant = $p['stock'] ?? $p['cantidad'] ?? $p['existencia'] ?? $p['existencias'] ?? 0;
        $precio = floatval($p['precio'] ?? 0);
        $valorTotalInventario += ($precio * $cant);
        if ($cant <= 5) {
            $alertasStock++;
        }
    }
} catch (Throwable $e) {
    // Si algo falla en la BD, la página sigue cargando limpiamente
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Tienda Minorista</title>
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

        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .metric-card { background: #0d0d0d; border: 1px solid #1f1f1f; border-radius: 16px; padding: 26px; box-shadow: 0 20px 40px rgba(0,0,0,0.8); }
        .metric-title { font-size: 11px; color: #777777; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 700; margin-bottom: 10px; }
        .metric-value { font-size: 32px; color: #ffffff; font-weight: 700; }

        /* Colores atenuados y elegantes para métricas */
        .metric-value.gold { color: #b89735; }
        .metric-value.red { color: #b34747; }

        .section-title { font-size: 15px; color: #aaaaaa; font-weight: 600; letter-spacing: 0.5px; margin-top: 10px; }

        .table-container { background: #0d0d0d; border: 1px solid #1f1f1f; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.9); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 18px 20px; border-bottom: 1px solid #161616; }
        th { background-color: #121212; color: #777777; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        td { font-size: 14px; color: #dddddd; }
        tr:hover td { background-color: #121212; }

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
                    <!-- Icono SVG de reporte / gráfico minimalista en B/N -->
                    <svg viewBox="0 0 24 24"><path d="M19 3H5C3.89 3 3 3.89 3 5V19C3 20.1 3.89 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.89 20.1 3 19 3M19 19H5V5H19V19M7 10H9V17H7V10M11 7H13V17H11V7M15 13H17V17H15V13Z"/></svg>
                </div>
                <h1>Reporte General de Ventas y Capital</h1>
            </div>
            <a href="login.php" class="btn-volver">← Panel Principal</a>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-title">Valor del Inventario</div>
                <div class="metric-value gold">$<?php echo number_format($valorTotalInventario, 2); ?></div>
            </div>
            <div class="metric-card">
                <div class="metric-title">Total de Productos</div>
                <div class="metric-value"><?php echo $totalProductos; ?></div>
            </div>
            <div class="metric-card">
                <div class="metric-title">Bajo Stock (Crítico)</div>
                <div class="metric-value <?php echo $alertasStock > 0 ? 'red' : ''; ?>">
                    <?php echo $alertasStock; ?>
                </div>
            </div>
        </div>

        <div class="section-title">Desglose de Activos</div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Unidades</th>
                        <th>Valor Total en Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaProductos as $p):
                        $cant = $p['stock'] ?? $p['cantidad'] ?? $p['existencia'] ?? $p['existencias'] ?? 0;
                        $precio = floatval($p['precio'] ?? 0);
                    ?>
                    <tr>
                        <td style="font-weight: 600; color: #ffffff;"><?php echo htmlspecialchars($p['nombre'] ?? 'Producto'); ?></td>
                        <td>$<?php echo number_format($precio, 2); ?></td>
                        <td><?php echo $cant; ?> und.</td>
                        <!-- Verde atenuado y sobrio para los montos totales -->
                        <td style="color: #3da860; font-weight: 600;">
                            $<?php echo number_format($precio * $cant, 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($listaProductos)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #666; padding: 25px;">No hay productos para mostrar en el reporte.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="page-footer">Sistema de Gestión Comercial v1.0 • Universidad Autónoma de Chiapas (UNACH)</div>

</body>
</html>
<?php
if (isset($db)) {
    $db->cerrarConexion();
}
?>