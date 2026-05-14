<?php
    // db_config.php
    $DB_HOST = 'localhost';
    $DB_NAME = 'mensajeria';
    $DB_USER = 'root';      // cámbialo por tu usuario de MySQL
    $DB_PASS = '';    // cámbialo por tu contraseña

    function getDB() {
        global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
        static $pdo = null;
        if ($pdo === null) {
            $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $pdo;
    }
?>