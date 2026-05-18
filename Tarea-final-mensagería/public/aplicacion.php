<?php
session_start();
require_once __DIR__ . "/../controladores/funciones.inc.php";

// Control de acceso: solo usuarios autenticados
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$hora    = $_SESSION['hora_conexion'];
$rol     = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal - Mensajería</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        <?= cssColorFondo() ?>
        label, legend {
            color: black;
        }
    </style>
</head>
<body>
    <header>
        <span>Usuario: <?= htmlspecialchars($usuario) ?> | Conectado desde <?= $hora ?></span>
        <nav>
            <a href="aplicacion.php">Inicio</a>
            <a href="preferencias.php">Preferencias</a>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <h2>Bienvenido, <?= htmlspecialchars($usuario) ?></h2>
        <p>Has iniciado sesión como <strong><?= $rol ?></strong>.</p>

        <section>
            <h3>Menú principal</h3>
            <ul>
                <li><a href="preferencias.php">Cambiar color de fondo (Preferencias)</a></li>
                <li>
                    <?php if ($rol === 'cliente'): ?>
                        <a href="cliente.php">Ir a mi panel de cliente</a>
                    <?php elseif ($rol === 'repartidor'): ?>
                        <a href="repartidor.php">Ir a mi panel de repartidor</a>
                    <?php elseif ($rol === 'admin'): ?>
                        <a href="admin.php">Ir al panel de administración</a>
                    <?php endif; ?>
                </li>
            </ul>
        </section>
    </main>
</body>
</html>