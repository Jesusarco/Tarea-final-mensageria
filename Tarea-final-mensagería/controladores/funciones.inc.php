<?php
    require_once __DIR__ . "/../config/db_config.php";

    /**
     * Busca un usuario por nombre en la base de datos.
     */
    function buscarUsuario(string $username): ?array {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT usuario, hash, rol FROM usuarios WHERE usuario = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return [
                'usuario' => $row['usuario'],
                'hash'    => $row['hash'],
                'rol'     => $row['rol']
            ];
        }
        return null;
    }

    /**
     * Devuelve todos los envíos de la base de datos.
     */
    function obtenerEnvios(): array {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT id, cliente, dir_recogida, dir_entrega, descripcion, foto, estado FROM envios ORDER BY id");
        $envios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $envios[] = [
                'id'             => $row['id'],
                'cliente'        => $row['cliente'],
                'dir_recogida'   => $row['dir_recogida'],
                'dir_entrega'    => $row['dir_entrega'],
                'descripcion'    => $row['descripcion'],
                'foto'           => $row['foto'],
                'estado'         => $row['estado']
            ];
        }
        return $envios;
    }

    /**
     * Guarda el array completo de envíos en la base de datos.
     * (Reemplaza todo el contenido de la tabla envios)
     */
    function guardarEnvios(array $envios): void {
        $pdo = getDB();
        try {
            $pdo->beginTransaction();
            // Borrar todos los envíos actuales
            $pdo->exec("DELETE FROM envios");
            // Insertar todos los envíos del array
            $stmt = $pdo->prepare("INSERT INTO envios (id, cliente, dir_recogida, dir_entrega, descripcion, foto, estado)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($envios as $e) {
                $stmt->execute([
                    $e['id'],
                    $e['cliente'],
                    $e['dir_recogida'],
                    $e['dir_entrega'],
                    $e['descripcion'],
                    $e['foto'],
                    $e['estado']
                ]);
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Valida los datos de un nuevo envío.
     */
    function validarEnvio(array $datos, array $archivo): array {
        $errores = [];

        if (empty(trim($datos['dir_recogida'] ?? ''))) {
            $errores['dir_recogida'] = 'La dirección de recogida es obligatoria.';
        }
        if (empty(trim($datos['dir_entrega'] ?? ''))) {
            $errores['dir_entrega'] = 'La dirección de entrega es obligatoria.';
        }
        if (empty(trim($datos['descripcion'] ?? ''))) {
            $errores['descripcion'] = 'La descripción es obligatoria.';
        }

        if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
            $errores['foto'] = 'Debe subir una foto del paquete.';
        } else {
            $mime = mime_content_type($archivo['tmp_name']);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
                $errores['foto'] = 'La foto debe ser JPEG, PNG o GIF.';
            }
            if ($archivo['size'] > 5 * 1024 * 1024) {
                $errores['foto'] = 'La foto es demasiado grande (máx. 5MB).';
            }
        }

        return $errores;
    }

    /*
     * Genera el CSS dinámico para aplicar el color de fondo guardado en cookie.
     */
    function cssColorFondo(): string {
        $color = $_COOKIE['color_fondo'] ?? '#ffffff';
        $hex = ltrim($color, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luminosidad = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        $texto = $luminosidad < 0.5 ? '#ffffff' : '#000000';
        $link = $luminosidad < 0.5 ? '#ecf0f1' : '#2c3e50';
        
        return "
            body {
                background-color: $color;
                color: $texto;
            }
            h1, h2, h3, p, label, legend {
                color: $texto;
            }
            a {
                color: $link;
            }
            form, table {
                background-color: #ffffff;
                color: #000000;
            }
            input, textarea, select {
                background-color: #ffffff;
                color: #000000;
            }
            .error {
                color: #e74c3c;
                background: #fce4e4;
            }
        ";
    }
?>