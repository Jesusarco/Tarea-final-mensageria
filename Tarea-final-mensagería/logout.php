<?php
session_start();
session_unset();
session_destroy();
// Eliminar cookie de sesión (opcional)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
// También eliminar la cookie del último usuario si se desea
// setcookie('ultimo_usuario', '', time() - 3600, "/");
header("Location: index.php");
exit;