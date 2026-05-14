<?php
    require_once __DIR__ . "/../controladores/funciones.inc.php";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Información - Mensajería</title>
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
            <a href="index.php">Volver al inicio</a>
        </header>

        <main>
            <h2>Funcionamiento de la aplicación</h2>
            <section>
                <h3>¿Para qué sirve?</h3>
                <p>Esta aplicación permite gestionar envíos de paquetería. Dependiendo de tu rol, podrás realizar distintas acciones:</p>
                <ul>
                    <li><strong>Cliente:</strong> realiza envíos, consulta el estado de los tuyos.</li>
                    <li><strong>Repartidor:</strong> recoge paquetes en domicilio y los entrega.</li>
                    <li><strong>Administrador:</strong> supervisa y modifica todos los envíos.</li>
                </ul>
            </section>
            <section>
                <h3>Roles y permisos</h3>
                <table>
                    <tr><th>Rol</th><th>Acciones</th></tr>
                    <tr><td>Cliente</td><td>Ver sus envíos, crear envío nuevo (estado “En espera”).</td></tr>
                    <tr><td>Repartidor</td><td>Ver envíos “En espera” y “En reparto”. Cambiar a “En reparto” o “Entregado”.</td></tr>
                    <tr><td>Admin</td><td>Ver todos los envíos, cambiar cualquier estado, eliminar envíos.</td></tr>
                </table>
            </section>
            <section>
                <h3>Inicio de sesión</h3>
                <p>Introduce tu usuario y contraseña en la página de inicio. Si no estás registrado, puedes acceder como invitado y ver esta información.</p>
            </section>
            <br>
            <section>
                <h3>El almacenamiento de los datos</h3>
                <p>Se hace medienta un fichero que se le va escribiendo por medio de fwrite. </p>
            </section>
        </main>
    </body>
</html>