<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {

    header("Location: login.html");

    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Mi perfil</title>

</head>

<body>

    <h1>👤 Mi perfil</h1>

    <h2>
        Bienvenido,
        <?= htmlspecialchars($_SESSION['nombre']) ?>
    </h2>

    <p>
        Correo:
        <?= htmlspecialchars($_SESSION['email']) ?>
    </p>

    <p>
        Rol:
        <?= htmlspecialchars($_SESSION['rol']) ?>
    </p>

    <a href="php/logout.php">
        Cerrar sesión
    </a>

</body>

</html>