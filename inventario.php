<?php
require_once 'conexion.php';
require_once 'Producto.php';

$db = new Conexion();
$productoObj = new Producto();
$listaProductos = $productoObj->obtenerTodos($db->getConexion());
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Inventario - Tienda Minorista</title>
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

        .header-actions { display: flex; gap: 12px; }

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

        .btn-nuevo {
            padding: 10px 20px;
            background: linear-gradient(135deg, #e5e5e5 0%, #a3a3a3 100%);
            color: #080808;
            font-weight: 700;
            text-decoration: none;
            border-radius: 10px;
            font-size: 13px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.25);
            transition: all 0.2s;
        }
        .btn-nuevo:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.4);
        }

        .table-container {
            background: #0d0d0d;
            border: 1px solid #1f1f1f;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.9);
        }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 18px 20px; border-bottom: 1px solid #161616; }
        th { background-color: #121212; color: #777777; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        td { font-size: 14px; color: #dddddd; }
        tr:hover td { background-color: #121212; }

        .badge-cat { background: #161616; color: #aaaaaa; padding: 4px 12px; border-radius: 20px; font-size: 11px; border: 1px solid #262626; }
        .stock-ok { color: #3da860; font-weight: 600; }
        .stock-low { color: #b34747; font-weight: 600; }

        .actions a { text-decoration: none; font-size: 13px; margin-right: 14px; font-weight: 600; transition: opacity 0.2s; }
        .actions a:hover { opacity: 0.8; }
        .btn-edit { color: #80bfff; }
        /* Rojo atenuado y sobrio unificado con los reportes */
        .btn-delete { color: #b34747; }

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
                    <svg viewBox="0 0 24 24"><path d="M21 16.5C21 16.88 20.79 17.21 20.47 17.38L12.57 21.82C12.41 21.91 12.21 22 12 22C11.79 22 11.59 21.91 11.43 21.82L3.53 17.38C3.21 17.21 3 16.88 3 16.5V7.5C3 7.12 3.21 6.79 3.53 6.62L11.43 2.18C11.59 2.09 11.79 2 12 2C12.21 2 12.41 2.09 12.57 2.18L20.47 6.62C20.79 6.79 21 7.12 21 7.5V16.5ZM12 4.15L5.12 8.01L12 11.87L18.88 8.01L12 4.15ZM5 15.91L11 19.27V12.74L5 9.41V15.91ZM13 19.27L19 15.91V9.41L13 12.74V19.27Z"/></svg>
                </div>
                <h1>Gestor de Inventario</h1>
            </div>
            <div class="header-actions">
                <a href="login.php" class="btn-volver">← Panel Principal</a>
                <a href="agregar_producto.php" class="btn-nuevo">+ Agregar Producto</a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaProductos as $p):
                        $stockVal = $p['stock'] ?? $p['cantidad'] ?? $p['existencia'] ?? $p['existencias'] ?? 0;
                    ?>
                    <tr>
                        <td>#<?php echo $p['id_producto'] ?? $p['id'] ?? '1'; ?></td>
                        <td style="font-weight: 600; color: #ffffff;"><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td><span class="badge-cat"><?php echo htmlspecialchars($p['categoria'] ?? 'General'); ?></span></td>
                        <td>$<?php echo number_format($p['precio'], 2); ?></td>
                        <td>
                            <span class="<?php echo ($stockVal <= 5) ? 'stock-low' : 'stock-ok'; ?>">
                                <?php echo $stockVal; ?> und.
                            </span>
                        </td>
                        <td class="actions">
                            <a href="#" class="btn-edit">Editar</a>
                            <a href="#" class="btn-delete">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($listaProductos)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #666; padding: 30px;">No hay productos registrados en la base de datos.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="page-footer">Sistema de Gestión Comercial v1.0 • Universidad Autónoma de Chiapas (UNACH)</div>

</body>
</html>
<?php $db->cerrarConexion(); ?>