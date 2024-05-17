<?php
session_start();
require_once __DIR__ . '/../Datos/DAOUsuario.php';

$error = '';

try {
    $daoUsuario = new DAOUsuario();
} catch (Exception $e) {
    $error = "Error de conexión: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    if (!$error) {
        $usuario = $daoUsuario->obtenerUsuarioPorCorreoYContrasena($correo, $contrasena);

        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            header("Location: index.php");
            exit;
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Almacén de Don Sil - Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h1>El Almacén de Don Sil</h1>
        <div class="avatar-container">
            <img src="img/login.png" alt="Avatar" class="avatar">
        </div>
        <form class="login-form" method="post" action="login.php">
            <div class="input-container">
                <input type="text" id="correo" name="correo" placeholder="Correo" required>
            </div>
            <div class="input-container">
                <input type="password" id="contrasena" name="contrasena" placeholder="Contraseña" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
            <?php if ($error && $_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <script>alert('<?php echo $error; ?>');</script>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
