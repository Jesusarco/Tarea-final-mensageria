<?php
session_start();
require_once "funciones.inc.php";

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: index.php");
    exit;
}

$mi_usuario = $_SESSION['usuario'];
$hora = $_SESSION['hora_conexion'];

// Obtener solo los envíos de este cliente
$envios = obtenerEnvios();
$mis_envios = array_filter($envios, function($e) use ($mi_usuario) {
    return $e['cliente'] === $mi_usuario;
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Cliente - Mensajería</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        <?= cssColorFondo() ?>
    </style>
</head>
<body>
    <header>
        Usuario: <?= htmlspecialchars($mi_usuario) ?> (Cliente) - Conectado desde <?= $hora ?>
        <a href="preferencias.php">Preferencias</a>
        <a href="logout.php">Cerrar sesión</a>
    </header>
    <h2>Mis envíos</h2>
    <?php if (empty($mis_envios)): ?>
        <p>No tienes envíos realizados.</p>
    <?php else: ?>
        <table>
            <tr><th>ID</th><th>Dirección recogida</th><th>Dirección entrega</th><th>Descripción</th><th>Foto</th><th>Estado</th></tr>
            <?php foreach ($mis_envios as $envio): ?>
            <tr>
                <td><?= $envio['id'] ?></td>
                <td><?= htmlspecialchars($envio['dir_recogida']) ?></td>
                <td><?= htmlspecialchars($envio['dir_entrega']) ?></td>
                <td><?= htmlspecialchars($envio['descripcion']) ?></td>
                <td><img src="uploads/<?= $envio['foto'] ?>" width="100" alt="Foto"></td>
                <td><?= $envio['estado'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <p><a href="nuevo_envio.php">Realizar nuevo envío</a></p>
</body>
</html>