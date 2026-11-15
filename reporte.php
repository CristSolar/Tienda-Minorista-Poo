<?php
require_once 'conexion.php';
require_once 'Producto.php';

$db = new Conexion();
$productoObj = new Producto();
$listaProductos = $productoObj->obtenerTodos($db->getConexion());

$totalProductos = count($listaProductos);
$valorTotalInventario = 0;
$alertasStock = 0;

foreach ($listaProductos as $p) {
    $valorTotalInventario += ($p['precio'] * $p['cantidad']);
    if ($p['cantidad'] <= 5) {
        $alertasStock++;
    }
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
        body { background-color: #080808; color: #e0e0e0; padding: 40px; min-height: 100vh; }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #222; padding-bottom: 20px; }
        h1 { font-size: 24px; color: #ffffff; font-weight: 600; }
        .btn-volver { padding: 10px 18px; background: #181818; color: #a0a0a0; text-decoration: none; border-radius: 8px; border: 1px solid #2e2e2e; font-size: 13px; font-weight: 600; }

        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 35px; }
        .metric-card { background: #111111; border: 1px solid #222222; border-radius: 12px; padding: 24px; }
        .metric-title { font-size: 11px; color: #777777; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 700; margin-bottom: 10px; }
        .metric-value { font-size: 32px; color: #ffffff; font-weight: 700; }
        .metric-value.gold { color: #d4af37; }
        .metric-value.red { color: #ff4d4d; }

        .table-container { background: #111111; border: 1px solid #222222; border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 16px 20px; border-bottom: 1px solid #1a1a1a; }
        th { background-color: #161616; color: #777777; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        td { font-size: 14px; color: #dddddd; }
    </style>
</head>
<body>

    <div class="header">
        <h1>📊 Reporte General de Ventas y Capital</h1>
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

    <h2 style="font-size: 18px; margin-bottom: 16px; color: #ffffff; font-weight: 600;">Desglose de Activos</h2>
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
                <?php foreach ($listaProductos as $p): ?>
                <tr>
                    <td style="font-weight: 600; color: #ffffff;"><?php echo htmlspecialchars($p['nombre']); ?></td>
                    <td>$<?php echo number_format($p['precio'], 2); ?></td>
                    <td><?php echo $p['cantidad']; ?> und.</td>
                    <td style="color: #00ff66; font-weight: 600;">
                        $<?php echo number_format($p['precio'] * $p['cantidad'], 2); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
<?php $db->cerrarConexion(); ?>