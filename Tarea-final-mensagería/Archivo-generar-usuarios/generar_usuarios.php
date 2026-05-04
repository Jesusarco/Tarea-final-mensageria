<?php
require_once "funciones.inc.php";
$usuarios = [
    "cliente1|".password_hash("cliente1", PASSWORD_DEFAULT)."|cliente",
    "cliente2|".password_hash("cliente2", PASSWORD_DEFAULT)."|cliente",
    "repartidor1|".password_hash("repartido", PASSWORD_DEFAULT)."|repartidor",
    "admin1|".password_hash("admin1", PASSWORD_DEFAULT)."|admin"
];
escribirLineas("datos/usuarios.txt", $usuarios);
echo "Usuarios creados.";