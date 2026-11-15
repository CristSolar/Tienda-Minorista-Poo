<?php
require_once 'conexion.php';

$db = new Conexion();
$conexion = $db->getConexion();

$sql = "SELECT id_usuario, usuario, rol FROM usuario ORDER BY id_usuario ASC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios - Tienda Minorista</title>
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

        /* Badge de Administrador en gris metálico con brillo potente */
        .role-badge { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
        .role-admin {
            background: linear-gradient(135deg, #e5e5e5 0%, #a3a3a3 100%);
            color: #080808;
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.45), 0 0 10px rgba(255, 255, 255, 0.3);
        }
        .role-comun { background: linear-gradient(135deg, #ffffff 0%, #b0b0b0 100%); color: #000000; }

        .status-dot { display: inline-block; width: 8px; height: 8px; background-color: #3da860; border-radius: 50%; margin-right: 6px; box-shadow: 0 0 8px rgba(61, 168, 96, 0.6); }

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
                    <!-- Icono SVG de usuarios minimalista en B/N -->
                    <svg viewBox="0 0 24 24"><path d="M16 11C17.66 11 18.99 9.66 18.99 8C18.99 6.34 17.66 5 16 5C14.34 5 13 6.34 13 8C13 9.66 14.34 11 16 11M8 11C9.66 11 10.99 9.66 10.99 8C10.99 6.34 9.66 5 8 5C6.34 5 5 6.34 5 8C5 9.66 6.34 11 8 11M16 13C13.33 13 8 14.33 8 17V20H24V17C24 14.33 18.67 13 16 13M8 13C7.68 13 7.34 13.02 7 13.05C9.1 14.2 10 15.5 10 17V19H6V17C6 15.42 4.41 14.16 2.65 13.62C2.23 13.22 2 12.67 2 12C2 10.9 2.9 10 4 10C5.1 10 6 10.9 6 12V13H8Z"/></svg>
                </div>
                <h1>Usuarios del Sistema</h1>
            </div>
            <a href="login.php" class="btn-volver">← Panel Principal</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Rol de Acceso</th>
                        <th>Estado de Cuenta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usr = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $usr['id_usuario']; ?></td>
                        <td style="font-weight: 600; color: #ffffff;"><?php echo htmlspecialchars($usr['usuario']); ?></td>
                        <td>
                            <?php if ($usr['rol'] == 'admin'): ?>
                                <span class="role-badge role-admin">Administrador</span>
                            <?php else: ?>
                                <span class="role-badge role-comun">Vendedor</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="status-dot"></span> Activo</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="page-footer">Sistema de Gestión Comercial v1.0 • Universidad Autónoma de Chiapas (UNACH)</div>

</body>
</html>
<?php $db->cerrarConexion(); ?>