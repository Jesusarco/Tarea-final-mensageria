<?php
session_start();
require_once "funciones.inc.php";

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'repartidor') {
    header("Location: index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$hora = $_SESSION['hora_conexion'];

// Procesar cambios de estado
if (isset($_POST['cambiar_estado']) && isset($_POST['id_envio']) && isset($_POST['nuevo_estado'])) {
    $id = $_POST['id_envio'];
    $nuevo = $_POST['nuevo_estado'];
    $envios = obtenerEnvios();
    foreach ($envios as &$envio) {
        if ($envio['id'] == $id) {
            if (($envio['estado'] === 'En espera' && $nuevo === 'En reparto') ||
                ($envio['estado'] === 'En reparto' && $nuevo === 'Entregado')) {
                $envio['estado'] = $nuevo;
            }
            break;
        }
    }
    unset($envio);
    guardarEnvios($envios);
    header("Location: repartidor.php");
    exit;
}

$envios = obtenerEnvios();
$en_espera = array_filter($envios, fn($e) => $e['estado'] === 'En espera');
$en_reparto = array_filter($envios, fn($e) => $e['estado'] === 'En reparto');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Repartidor</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        <?= cssColorFondo() ?>
    </style>
</head>
<body>
    <header>
        Usuario: <?= htmlspecialchars($usuario) ?> (Repartidor) - Conectado desde <?= $hora ?>
        <a href="preferencias.php">Preferencias</a>
        <a href="logout.php">Cerrar sesión</a>
    </header>
    <h2>Envíos en espera</h2>
    <?php if (empty($en_espera)): ?>
        <p>No hay envíos en espera.</p>
    <?php else: ?>
        <table>
            <tr><th>ID</th><th>Cliente</th><th>Recogida</th><th>Entrega</th><th>Descripción</th><th>Foto</th><th>Acción</th></tr>
            <?php foreach ($en_espera as $e): ?>
            <tr>
                <td><?= $e['id'] ?></td>
                <td><?= htmlspecialchars($e['cliente']) ?></td>
                <td><?= htmlspecialchars($e['dir_recogida']) ?></td>
                <td><?= htmlspecialchars($e['dir_entrega']) ?></td>
                <td><?= htmlspecialchars($e['descripcion']) ?></td>
                <td><img src="uploads/<?= $e['foto'] ?>" width="100"></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="id_envio" value="<?= $e['id'] ?>">
                        <input type="hidden" name="nuevo_estado" value="En reparto">
                        <input type="submit" name="cambiar_estado" value="Pasar a En reparto">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h2>Envíos en reparto</h2>
    <?php if (empty($en_reparto)): ?>
        <p>No hay envíos en reparto.</p>
    <?php else: ?>
        <table>
            <tr><th>ID</th><th>Cliente</th><th>Recogida</th><th>Entrega</th><th>Descripción</th><th>Foto</th><th>Acción</th></tr>
            <?php foreach ($en_reparto as $e): ?>
            <tr>
                <td><?= $e['id'] ?></td>
                <td><?= htmlspecialchars($e['cliente']) ?></td>
                <td><?= htmlspecialchars($e['dir_recogida']) ?></td>
                <td><?= htmlspecialchars($e['dir_entrega']) ?></td>
                <td><?= htmlspecialchars($e['descripcion']) ?></td>
                <td><img src="uploads/<?= $e['foto'] ?>" width="100"></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="id_envio" value="<?= $e['id'] ?>">
                        <input type="hidden" name="nuevo_estado" value="Entregado">
                        <input type="submit" name="cambiar_estado" value="Marcar como Entregado">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>