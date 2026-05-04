<?php
session_start();
require_once "funciones.inc.php";

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$hora = $_SESSION['hora_conexion'];

// Acciones del admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $envios = obtenerEnvios();
    if (isset($_POST['cambiar_estado'])) {
        $id = $_POST['id_envio'];
        $nuevo = $_POST['nuevo_estado'];
        foreach ($envios as &$envio) {
            if ($envio['id'] == $id) {
                $envio['estado'] = $nuevo;
                break;
            }
        }
        unset($envio);
        guardarEnvios($envios);
    } elseif (isset($_POST['eliminar'])) {
        $id = $_POST['id_envio'];
        $envios = array_filter($envios, fn($e) => $e['id'] != $id);
        guardarEnvios($envios);
    }
    header("Location: admin.php");
    exit;
}

$envios = obtenerEnvios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        <?= cssColorFondo() ?>
    </style>
</head>
<body>
    <header>
        Usuario: <?= htmlspecialchars($usuario) ?> (Admin) - Conectado desde <?= $hora ?>
        <a href="preferencias.php">Preferencias</a>
        <a href="logout.php">Cerrar sesión</a>
    </header>
    <h2>Gestión de envíos</h2>
    <table>
        <tr><th>ID</th><th>Cliente</th><th>Recogida</th><th>Entrega</th><th>Descripción</th><th>Foto</th><th>Estado</th><th>Cambiar a</th><th>Eliminar</th></tr>
        <?php foreach ($envios as $e): ?>
        <tr>
            <td><?= $e['id'] ?></td>
            <td><?= htmlspecialchars($e['cliente']) ?></td>
            <td><?= htmlspecialchars($e['dir_recogida']) ?></td>
            <td><?= htmlspecialchars($e['dir_entrega']) ?></td>
            <td><?= htmlspecialchars($e['descripcion']) ?></td>
            <td><img src="uploads/<?= $e['foto'] ?>" width="100"></td>
            <td><?= $e['estado'] ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id_envio" value="<?= $e['id'] ?>">
                    <select name="nuevo_estado">
                        <option value="En espera" <?= $e['estado']=='En espera'?'selected':''?>>En espera</option>
                        <option value="En reparto" <?= $e['estado']=='En reparto'?'selected':''?>>En reparto</option>
                        <option value="Entregado" <?= $e['estado']=='Entregado'?'selected':''?>>Entregado</option>
                    </select>
                    <input type="submit" name="cambiar_estado" value="Actualizar">
                </form>
            </td>
            <td>
                <form method="post" onsubmit="return confirm('¿Eliminar envío?');">
                    <input type="hidden" name="id_envio" value="<?= $e['id'] ?>">
                    <input type="submit" name="eliminar" value="Eliminar">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>