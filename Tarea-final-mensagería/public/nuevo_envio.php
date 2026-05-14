<?php
    session_start();
    require_once __DIR__ . "/../controladores/funciones.inc.php";

    if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'cliente') {
        header("Location: index.php");
        exit;
    }

    // Inicializar variables para conservar datos
    $dir_recogida = '';
    $dir_entrega  = '';
    $descripcion  = '';
    $errores      = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dir_recogida = trim($_POST['dir_recogida'] ?? '');
        $dir_entrega  = trim($_POST['dir_entrega'] ?? '');
        $descripcion  = trim($_POST['descripcion'] ?? '');

        // Validar usando la función centralizada
        $errores = validarEnvio($_POST, $_FILES['foto'] ?? []);

        // Si no hay errores, guardar el envío
        if (empty($errores)) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nombre_foto = uniqid("envio_") . "." . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $nombre_foto);

            $envios = obtenerEnvios();
            $nuevo_id = count($envios) > 0 ? max(array_column($envios, 'id')) + 1 : 1;
            $nuevo_envio = [
                'id'             => $nuevo_id,
                'cliente'        => $_SESSION['usuario'],
                'dir_recogida'   => $dir_recogida,
                'dir_entrega'    => $dir_entrega,
                'descripcion'    => $descripcion,
                'foto'           => $nombre_foto,
                'estado'         => 'En espera'
            ];
            $envios[] = $nuevo_envio;
            guardarEnvios($envios);
            header("Location: cliente.php");
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Nuevo envío - Mensajería</title>
        <link rel="stylesheet" href="estilos.css">
        <style>
            <?= cssColorFondo() ?>
            .error-campo {
                color: #e74c3c;
                font-size: 0.9em;
                margin-top: 2px;
            }
            label, legend {
                color: black;
            }
            .campo-error input, .campo-error textarea {
                border-color: #e74c3c;
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const primerError = document.querySelector(".campo-error input, .campo-error textarea");
                if (primerError) {
                    primerError.focus();
                }
            });
        </script>
    </head>
    <body>
        <header>
            Usuario: <?= htmlspecialchars($_SESSION['usuario']) ?> (Cliente) - 
            <a href="cliente.php">Volver al panel</a>
        </header>

        <main>
            <h2>Nuevo envío</h2>
            <form method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend>Datos del envío</legend>

                    <div class="<?= isset($errores['dir_recogida']) ? 'campo-error' : '' ?>">
                        <label for="dir_recogida">Dirección de recogida:</label><br>
                        <input type="text" name="dir_recogida" id="dir_recogida"
                            value="<?= htmlspecialchars($dir_recogida) ?>" maxlength="200"><br>
                        <?php if (isset($errores['dir_recogida'])): ?>
                            <span class="error-campo"><?= $errores['dir_recogida'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="<?= isset($errores['dir_entrega']) ? 'campo-error' : '' ?>">
                        <label for="dir_entrega">Dirección de entrega:</label><br>
                        <input type="text" name="dir_entrega" id="dir_entrega"
                            value="<?= htmlspecialchars($dir_entrega) ?>" maxlength="200"><br>
                        <?php if (isset($errores['dir_entrega'])): ?>
                            <span class="error-campo"><?= $errores['dir_entrega'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="<?= isset($errores['descripcion']) ? 'campo-error' : '' ?>">
                        <label for="descripcion">Descripción:</label><br>
                        <textarea name="descripcion" id="descripcion" rows="4" maxlength="500"><?= htmlspecialchars($descripcion) ?></textarea><br>
                        <?php if (isset($errores['descripcion'])): ?>
                            <span class="error-campo"><?= $errores['descripcion'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="<?= isset($errores['foto']) ? 'campo-error' : '' ?>">
                        <label for="foto">Foto del paquete:</label><br>
                        <input type="file" name="foto" id="foto" accept="image/*"><br>
                        <?php if (isset($errores['foto'])): ?>
                            <span class="error-campo"><?= $errores['foto'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div>
                        <input type="submit" name="enviar" value="Crear envío">
                    </div>
                </fieldset>
            </form>
        </main>
    </body>
</html>