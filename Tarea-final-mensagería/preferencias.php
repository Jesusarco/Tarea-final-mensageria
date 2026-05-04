<?php
session_start();
require_once "funciones.inc.php";

// Solo accesible si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$hora    = $_SESSION['hora_conexion'];

// Color por defecto
$color_fondo = "#ffffff"; // blanco

// Procesar el formulario de cambio de color
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['color'])) {
        $color = trim($_POST['color']);
        // Validación básica: formato hexadecimal de 7 caracteres (ej: #aabbcc)
        if (preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
            setcookie('color_fondo', $color, time() + 86400 * 30, "/"); // 30 días
            $color_fondo = $color;
            // Redirigir para evitar reenvío del formulario
            header("Location: preferencias.php");
            exit;
        }
    } elseif (isset($_POST['restablecer'])) {
        // Eliminar la cookie
        setcookie('color_fondo', '', time() - 3600, "/");
        header("Location: preferencias.php");
        exit;
    }
}

// Si hay cookie, usar su valor
if (isset($_COOKIE['color_fondo'])) {
    $color_fondo = $_COOKIE['color_fondo'];
}

// Función para determinar si un color es oscuro (para texto legible)
function esColorOscuro($hex) {
    $hex = ltrim($hex, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $luminosidad = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    return $luminosidad < 0.5;
}
$texto_claro = esColorOscuro($color_fondo) ? 'color: #ffffff;' : 'color: #000000;';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Preferencias - Mensajería</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Aplicar el color de fondo a toda la página */
        body {
            background-color: <?= $color_fondo ?>;
            <?= $texto_claro ?>;
        }
        /* Ajustar algunos elementos para que hereden el color de texto */
        h1, h2, h3, p, label, legend, td, th {
            <?= $texto_claro ?>;
        }
        /* Preservar el color de los enlaces y botones */
        a {
            color: <?= esColorOscuro($color_fondo) ? '#ecf0f1' : '#2c3e50' ?>;
        }
        input[type="submit"] {
            background-color: #2c3e50;
            color: white;
        }
        .error {
            color: #e74c3c;
            background: #fce4e4;
        }
    </style>
</head>
<body>
    <header style="background-color: <?= esColorOscuro($color_fondo) ? '#1a252f' : '#2c3e50' ?>;">
        <span>Usuario: <?= htmlspecialchars($usuario) ?> | Conectado desde <?= $hora ?></span>
        <nav>
            <a href="cliente.php" <?php if ($_SESSION['rol'] == 'cliente') echo 'style="display:inline;"'; else echo 'style="display:none;"'; ?>>Mi panel</a>
            <a href="repartidor.php" <?php if ($_SESSION['rol'] == 'repartidor') echo 'style="display:inline;"'; else echo 'style="display:none;"'; ?>>Mi panel</a>
            <a href="admin.php" <?php if ($_SESSION['rol'] == 'admin') echo 'style="display:inline;"'; else echo 'style="display:none;"'; ?>>Mi panel</a>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <h2>Preferencias de visualización</h2>
        <p>Color de fondo actual: <span style="background-color: <?= $color_fondo ?>; padding: 2px 10px; border: 1px solid #ccc;"><?= $color_fondo ?></span></p>
        
        <form method="post" action="preferencias.php">
            <fieldset>
                <legend>Seleccionar color de fondo</legend>
                <label for="color">Color:</label>
                <select name="color" id="color">
                    <option value="#ffffff" <?= $color_fondo == '#ffffff' ? 'selected' : '' ?>>Blanco</option>
                    <option value="#f0f0f0" <?= $color_fondo == '#f0f0f0' ? 'selected' : '' ?>>Gris claro</option>
                    <option value="#d9ead3" <?= $color_fondo == '#d9ead3' ? 'selected' : '' ?>>Verde claro</option>
                    <option value="#cfe2f3" <?= $color_fondo == '#cfe2f3' ? 'selected' : '' ?>>Azul claro</option>
                    <option value="#fce5cd" <?= $color_fondo == '#fce5cd' ? 'selected' : '' ?>>Naranja claro</option>
                    <option value="#ead1dc" <?= $color_fondo == '#ead1dc' ? 'selected' : '' ?>>Rosa claro</option>
                    <option value="#444444" <?= $color_fondo == '#444444' ? 'selected' : '' ?>>Gris oscuro</option>
                    <option value="#1c2833" <?= $color_fondo == '#1c2833' ? 'selected' : '' ?>>Azul oscuro</option>
                </select>
                <input type="submit" name="guardar" value="Guardar">
            </fieldset>
        </form>

        <form method="post" action="preferencias.php" style="margin-top: 20px;">
            <input type="hidden" name="restablecer" value="1">
            <input type="submit" value="Restablecer preferencias (blanco)">
        </form>

        <p><a href="<?php
            switch ($_SESSION['rol']) {
                case 'cliente': echo 'cliente.php'; break;
                case 'repartidor': echo 'repartidor.php'; break;
                case 'admin': echo 'admin.php'; break;
            }
        ?>">Volver al panel</a></p>
    </main>
</body>
</html>