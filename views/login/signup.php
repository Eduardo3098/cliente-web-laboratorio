<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo constant('URL'); ?>/public/css/login.css">
    <title>Signup</title>
</head>
<body>
    <?php require 'views/header.php'; ?>
    <?php $this->showMessages();?>
    <div id="login-main">
        <form action="<?php echo constant('URL'); ?>/signup/newUser" method="POST">
        <div align="center"><h1><strong>Administrador de Gastos</strong></h1></div>
            <h2>Registrarse</h2>
            <p>
                <label for="username">Usuario:</label>
                <input type="text" name="username" id="username">
            </p>
            <p>
                <label for="password">Contraseña:</label>
                <input type="text" name="password" id="password">
            </p>
            <p>
                <input type="submit" value="Iniciar sesión" />
            </p>
            <p>
                ¿Tienes una cuenta? <a href="<?php echo constant('URL'); ?>">Iniciar sesion</a>
            </p>
        </form>
    </div>
</body>
</html>