<?php
    require_once __DIR__ . "/../controladores/funciones.inc.php";

    $usuarios = [
        ["cliente1",   password_hash("cliente1", PASSWORD_DEFAULT),   "cliente"],
        ["cliente2",   password_hash("cliente2", PASSWORD_DEFAULT),   "cliente"],
        ["repartidor1", password_hash("repartido", PASSWORD_DEFAULT), "repartidor"],
        ["admin1",     password_hash("admin1", PASSWORD_DEFAULT),     "admin"]
    ];

    try {
        $pdo = getDB();
        $pdo->exec("DELETE FROM usuarios"); // Limpia la tabla
        $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, hash, rol) VALUES (?, ?, ?)");
        foreach ($usuarios as $u) {
            $stmt->execute($u);
        }
        echo "Usuarios creados correctamente en la base de datos.";
    } catch (PDOException $e) {
        echo "Error al insertar usuarios: " . $e->getMessage();
    }
?>