<?php
    session_start();
    require_once __DIR__ . "/../controladores/funciones.inc.php";

    // Cookie para recordar el último usuario (30 días)
    $ultimo_usuario = $_COOKIE['ultimo_usuario'] ?? '';

    $error = "";

    // Procesar el formulario de login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Acceso como invitado
        if (isset($_POST['invitado'])) {
            // No se inicia sesión, se redirige a información
            header("Location: informacion.php");
            exit;
        }

        // Login normal
        $username = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        // Guardar el nombre en cookie 
        setcookie('ultimo_usuario', $username, time() + 86400 * 30, "/");

        if (empty($username) || empty($password)) {
            $error = "Debes introducir un nombre de usuario y una contraseña.";
        } else {
            // Buscar en el fichero de usuarios
            $usuario = buscarUsuario($username);
            if ($usuario && password_verify($password, $usuario['hash'])) {
                // Login correcto
                session_regenerate_id(true);
                $_SESSION['usuario'] = $username;
                $_SESSION['rol']     = $usuario['rol'];
                $_SESSION['hora_conexion'] = date("d/m/Y H:i:s");

                // Redirigir a la página principal (aplicacion.php)
                header("Location: aplicacion.php");
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Login - Mensajería</title>
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
            <h1>Empresa de Mensajería</h1>
            <nav>
                <a href="informacion.php">Información</a>
            </nav>
        </header>

        <main>
            <h2>Iniciar sesión</h2>

            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post" action="index.php">
                <fieldset>
                    <legend>Acceso de usuarios registrados</legend>
                    <div>
                        <label for="usuario">Usuario:</label><br>
                        <input type="text" name="usuario" id="usuario"
                            value="<?= htmlspecialchars($ultimo_usuario) ?>" maxlength="50"><br>
                    </div>
                    <div>
                        <label for="password">Contraseña:</label><br>
                        <input type="password" name="password" id="password" maxlength="50"><br>
                    </div>
                    <div>
                        <input type="submit" name="login" value="Entrar">
                    </div>
                </fieldset>
            </form>

            <form method="post" action="index.php" style="margin-top: 15px;">
                <input type="hidden" name="invitado" value="1">
                <input type="submit" value="Acceder como invitado">
            </form>
        </main>
    </body>
</html>