<?php
/**
 * Lee un fichero de texto y devuelve un array con cada línea.
 */
function leerLineas(string $ruta): array {
    if (!file_exists($ruta)) return [];
    $contenido = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $contenido !== false ? $contenido : [];
}

/**
 * Escribe un array de líneas en un fichero (sobrescribe).
 */
function escribirLineas(string $ruta, array $lineas): void {
    $fich = fopen($ruta, "w+");
    if ($fich) {
        foreach ($lineas as $linea) {
            fwrite($fich, $linea . PHP_EOL);
        }
        fclose($fich);
    }
}

/**
 * Busca un usuario por nombre y devuelve sus datos o null.
 */
function buscarUsuario(string $username): ?array {
    $lineas = leerLineas("datos/usuarios.txt");
    foreach ($lineas as $linea) {
        $datos = explode("|", trim($linea));
        if (count($datos) >= 3 && $datos[0] === $username) {
            return [
                'usuario' => $datos[0],
                'hash'    => $datos[1],
                'rol'     => $datos[2]
            ];
        }
    }
    return null;
}

/**
 * Devuelve todos los envíos como array asociativo.
 */
function obtenerEnvios(): array {
    $lineas = leerLineas("datos/envios.txt");
    $envios = [];
    foreach ($lineas as $linea) {
        $campos = explode("|", trim($linea));
        if (count($campos) >= 7) {
            $envios[] = [
                'id'             => $campos[0],
                'cliente'        => $campos[1],
                'dir_recogida'   => $campos[2],
                'dir_entrega'    => $campos[3],
                'descripcion'    => $campos[4],
                'foto'           => $campos[5],
                'estado'         => $campos[6]
            ];
        }
    }
    return $envios;
}

/**
 * Guarda un array de envíos en el fichero.
 */
function guardarEnvios(array $envios): void {
    $lineas = [];
    foreach ($envios as $e) {
        $lineas[] = implode("|", [
            $e['id'],
            $e['cliente'],
            $e['dir_recogida'],
            $e['dir_entrega'],
            $e['descripcion'],
            $e['foto'],
            $e['estado']
        ]);
    }
    escribirLineas("datos/envios.txt", $lineas);
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


/**
 * Genera el CSS dinámico para aplicar el color de fondo guardado en cookie.
 * Devuelve una cadena con las reglas CSS para insertar en el <style>.
 */
function cssColorFondo(): string {
    $color = $_COOKIE['color_fondo'] ?? '#ffffff';
    // Texto claro si el fondo es oscuro
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
        h1, h2, h3, p, label, legend, {
            color: $texto;
        }
        a {
            color: $link;
        }
        /* Los formularios y tablas mantienen fondo blanco para legibilidad */
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