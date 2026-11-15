<?php
require_once 'conexion.php';
require_once 'Usuario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

$usuario_ingresado = $_POST['usuario'] ?? $_POST['username'] ?? '';
$password_ingresado = $_POST['password'] ?? '';

$db = new Conexion();
$conexion = $db->getConexion();
$usuarioObj = new Usuario($usuario_ingresado, $password_ingresado);

$esValido = $usuarioObj->autenticar($conexion);
$rol = $usuarioObj->getRol();

$totalProductos = 0;
$valorInventario = 0;
if ($esValido) {
    try {
        $res = $conexion->query("SELECT COUNT(*) as total, SUM(precio * COALESCE(cantidad, stock, 0)) as valor FROM productos");
        if ($res && $row = $res->fetch_assoc()) {
            $totalProductos = intval($row['total']);
            $valorInventario = floatval($row['valor']);
        }
    } catch (Throwable $e) {}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Tienda Minorista</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            /* Desvanecido radial mucho más pronunciado y profundo */
            background: radial-gradient(circle at center, #1d1d1d 0%, #020202 85%);
            color: #d0d0d0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        .navbar {
            width: 100%;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            background-color: rgba(8, 8, 8, 0.9);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-brand {
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 1.5px;
            color: #ffffff;
            text-transform: uppercase;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .status-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: #888888;
            background: #121212;
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid #202020;
            letter-spacing: 0.5px;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            background-color: #00ff66;
            border-radius: 50%;
            box-shadow: 0 0 8px #00ff66;
        }

        .btn-logout-nav {
            padding: 7px 16px;
            background: #161616;
            color: #aaaaaa;
            border: 1px solid #262626;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-logout-nav:hover {
            color: #ffffff;
            background: #202020;
            border-color: #444444;
        }

        .main-container {
            max-width: 1100px;
            width: 100%;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 30px;
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .welcome-banner {
            background-color: #0d0d0d;
            border: 1px solid #1f1f1f;
            border-radius: 16px;
            padding: 30px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 25px 50px rgba(0,0,0,0.9);
        }

        .welcome-user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .avatar-box {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background-color: #161616;
            border: 1px solid #262626;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }

        .welcome-text h2 {
            font-size: 20px;
            color: #ffffff;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .welcome-text p {
            color: #777777;
            font-size: 13px;
        }

        .role-badge {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* Administrador en gris metálico con brillo potente */
        .role-admin {
            background: linear-gradient(135deg, #e5e5e5 0%, #a3a3a3 100%);
            color: #080808;
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.45), 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .role-comun {
            background: linear-gradient(135deg, #ffffff 0%, #b0b0b0 100%);
            color: #0a0a0a;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }

        .role-error {
            background-color: #2a1212;
            color: #ff6b6b;
            border: 1px solid #552222;
        }

        .metrics-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .metric-mini-card {
            background-color: #0d0d0d;
            border: 1px solid #1f1f1f;
            border-radius: 14px;
            padding: 20px 24px;
        }

        .metric-mini-title {
            font-size: 11px;
            color: #777777;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .metric-mini-value {
            font-size: 24px;
            color: #ffffff;
            font-weight: 700;
        }

        .metric-mini-value.gold { color: #d4af37; }

        .section-title {
            font-size: 15px;
            color: #aaaaaa;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .btn-action {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 26px;
            background-color: #0d0d0d;
            color: #ffffff;
            border: 1px solid #1f1f1f;
            border-radius: 16px;
            text-decoration: none;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-action:hover {
            background-color: #121212;
            border-color: #383838;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
        }

        .action-icon-title {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        /* Iconos minimalistas en B/N (SVG blanco/gris) */
        .action-icon-title svg {
            width: 20px;
            height: 20px;
            fill: #cccccc;
        }

        .action-desc {
            font-size: 13px;
            color: #777777;
            line-height: 1.5;
        }

        .action-arrow {
            align-self: flex-end;
            font-size: 16px;
            color: #555555;
            transition: transform 0.2s ease;
        }

        .btn-action:hover .action-arrow {
            transform: translateX(4px);
            color: #ffffff;
        }

        .error-card {
            background-color: #0d0d0d;
            border: 1px solid #2a1212;
            border-radius: 16px;
            padding: 50px;
            text-align: center;
            max-width: 480px;
            margin: 60px auto;
            box-shadow: 0 30px 60px rgba(0,0,0,0.9);
        }

        .error-card h2 { color: #ff6b6b; font-size: 22px; margin-bottom: 10px; }
        .error-card p { color: #888888; font-size: 14px; margin-bottom: 24px; }

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

    <nav class="navbar">
        <div class="nav-brand">Tienda Minorista POO</div>
        <div class="nav-right">
            <div class="status-pill">
                <div class="status-dot"></div>
                Sistema Activo
            </div>
            <?php if ($esValido): ?>
                <a href="index.html" class="btn-logout-nav">Cerrar Sesión</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="main-container">
    <?php
    if ($esValido) {
        $nombreUser = htmlspecialchars($usuarioObj->getUsuario());
        $inicial = strtoupper(substr($nombreUser, 0, 1));
    ?>
        <div class="welcome-banner">
            <div class="welcome-user-info">
                <div class="avatar-box"><?php echo $inicial; ?></div>
                <div class="welcome-text">
                    <h2>¡Bienvenido, <?php echo $nombreUser; ?>!</h2>
                    <p>Sesión iniciada correctamente en el sistema corporativo.</p>
                </div>
            </div>
            <div>
                <?php if ($rol == 'admin'): ?>
                    <span class="role-badge role-admin">Administrador</span>
                <?php else: ?>
                    <span class="role-badge role-comun">Vendedor</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="metrics-overview">
            <div class="metric-mini-card">
                <div class="metric-mini-title">Productos Registrados</div>
                <div class="metric-mini-value"><?php echo $totalProductos; ?></div>
            </div>
            <div class="metric-mini-card">
                <div class="metric-mini-title">Valor Global en Stock</div>
                <div class="metric-mini-value gold">$<?php echo number_format($valorInventario, 2); ?></div>
            </div>
            <div class="metric-mini-card">
                <div class="metric-mini-title">Rol del Sistema</div>
                <div class="metric-mini-value" style="font-size: 18px; padding-top: 4px; text-transform: uppercase;"><?php echo $rol; ?></div>
            </div>
        </div>

        <div>
            <div class="section-title">Módulos de Gestión Disponibles</div>
            <p style="color: #666; font-size: 12px; margin-bottom: 16px;">Selecciona una de las herramientas operativas para continuar.</p>

            <div class="actions-grid">
            <?php if ($rol == 'admin'): ?>
                <a href="inventario.php" class="btn-action">
                    <div>
                        <div class="action-icon-title">
                            <!-- Icono SVG caja minimalista B/N -->
                            <svg viewBox="0 0 24 24"><path d="M21 16.5C21 16.88 20.79 17.21 20.47 17.38L12.57 21.82C12.41 21.91 12.21 22 12 22C11.79 22 11.59 21.91 11.43 21.82L3.53 17.38C3.21 17.21 3 16.88 3 16.5V7.5C3 7.12 3.21 6.79 3.53 6.62L11.43 2.18C11.59 2.09 11.79 2 12 2C12.21 2 12.41 2.09 12.57 2.18L20.47 6.62C20.79 6.79 21 7.12 21 7.5V16.5ZM12 4.15L5.12 8.01L12 11.87L18.88 8.01L12 4.15ZM5 15.91L11 19.27V12.74L5 9.41V15.91ZM13 19.27L19 15.91V9.41L13 12.74V19.27Z"/></svg>
                            Gestor de Inventario
                        </div>
                        <div class="action-desc">Control completo de altas, modificaciones, precios y existencias físicas de mercancía.</div>
                    </div>
                    <div class="action-arrow">→</div>
                </a>
                <a href="reportes.php" class="btn-action">
                    <div>
                        <div class="action-icon-title">
                            <!-- Icono SVG gráfico minimalista B/N -->
                            <svg viewBox="0 0 24 24"><path d="M19 3H5C3.89 3 3 3.89 3 5V19C3 20.1 3.89 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.89 20.1 3 19 3M19 19H5V5H19V19M7 10H9V17H7V10M11 7H13V17H11V7M15 13H17V17H15V13Z"/></svg>
                            Reporte de Ventas
                        </div>
                        <div class="action-desc">Evaluación general de capital, valorización de activos y alertas de stock crítico.</div>
                    </div>
                    <div class="action-arrow">→</div>
                </a>
                <a href="usuarios.php" class="btn-action">
                    <div>
                        <div class="action-icon-title">
                            <!-- Icono SVG usuarios minimalista B/N -->
                            <svg viewBox="0 0 24 24"><path d="M16 11C17.66 11 18.99 9.66 18.99 8C18.99 6.34 17.66 5 16 5C14.34 5 13 6.34 13 8C13 9.66 14.34 11 16 11M8 11C9.66 11 10.99 9.66 10.99 8C10.99 6.34 9.66 5 8 5C6.34 5 5 6.34 5 8C5 9.66 6.34 11 8 11M16 13C13.33 13 8 14.33 8 17V20H24V17C24 14.33 18.67 13 16 13M8 13C7.68 13 7.34 13.02 7 13.05C9.1 14.2 10 15.5 10 17V19H6V17C6 15.42 4.41 14.16 2.65 13.62C2.23 13.22 2 12.67 2 12C2 10.9 2.9 10 4 10C5.1 10 6 10.9 6 12V13H8Z"/></svg>
                            Administrar Usuarios
                        </div>
                        <div class="action-desc">Control de cuentas de acceso, credenciales y privilegios operativos del personal.</div>
                    </div>
                    <div class="action-arrow">→</div>
                </a>
            <?php else: ?>
                <a href="venta.php" class="btn-action">
                    <div>
                        <div class="action-icon-title">
                            <!-- Icono SVG carrito minimalista B/N -->
                            <svg viewBox="0 0 24 24"><path d="M17 18C15.89 18 15 18.89 15 20C15 21.11 15.89 22 17 22C18.11 22 19 21.11 19 20C19 18.89 18.11 18 17 18M1 2V4H3L6.6 11.59L5.25 14.04C5.09 14.32 5 14.65 5 15C5 16.1 5.9 17 7 17H19V15H7.42C7.29 15 7.18 14.89 7.18 14.76L7.2 14.63L8.1 13H15.55C16.3 13 16.96 12.58 17.3 11.97L21.88 3.5C21.95 3.34 22 3.17 22 3C22 2.45 21.55 2 21 2H4.21L3.27 2H1M7 18C5.89 18 5 18.89 5 20C5 21.11 5.89 22 7 22C8.11 22 9 21.11 9 20C9 18.89 8.11 18 7 18Z"/></svg>
                            Registrar Nueva Venta
                        </div>
                        <div class="action-desc">Procesar operaciones comerciales de salida de mercancía y cobro directo.</div>
                    </div>
                    <div class="action-arrow">→</div>
                </a>
                <a href="catalogo.php" class="btn-action">
                    <div>
                        <div class="action-icon-title">
                            <!-- Icono SVG catálogo minimalista B/N -->
                            <svg viewBox="0 0 24 24"><path d="M19 3H5C3.89 3 3 3.89 3 5V19C3 20.1 3.89 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.89 20.1 3 19 3M19 5V19H5V5H19M7 7H17V9H7V7M7 11H17V13H7V11M7 15H14V17H7V15Z"/></svg>
                            Ver Catálogo Disponible
                        </div>
                        <div class="action-desc">Consulta visual rápida de precios, categorías y disponibilidad de stock en tiempo real.</div>
                    </div>
                    <div class="action-arrow">→</div>
                </a>
            <?php endif; ?>
            </div>
        </div>

    <?php
    } else {
    ?>
        <div class="error-card">
            <div class="avatar-box" style="margin: 0 auto 20px auto; color: #ff6b6b; border-color: #552222;">✕</div>
            <h2>Acceso Denegado</h2>
            <p>El usuario o la contraseña ingresados no existen en la base de datos o son incorrectos.</p>
            <a href="index.html" class="btn-logout-nav" style="display: inline-block; padding: 10px 24px;">Volver al Acceso</a>
        </div>
    <?php
    }
    ?>
    </div>

    <div class="page-footer">Sistema de Gestión Comercial v1.0 • Universidad Autónoma de Chiapas (UNACH)</div>

</body>
</html>
<?php
if (isset($db)) {
    $db->cerrarConexion();
}
?>