<?php
require_once __DIR__ . '/../Datos/DAOProducto.php';

$daoProducto = new DAOProducto();
$productos = $daoProducto->obtenerProductos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio - El Almacén de Don Sil</title>
    <link rel="stylesheet" href="css/index.css">
    <script src="js/ventas.js" defer></script>
</head>
<body>
    <div class="content">
        <div class="navbar">
            <div class="navbar-left">
                <a href="index.php">Inicio</a>
                <a href="gestion-productos.php">Gestión de Productos</a>
                <a href="ventas.php" class="active">Ventas</a>
                <a href="registro.php">Registro</a>
            </div>
            <div class="navbar-right">
                <a href="login.php" class="logout">Cerrar Sesión</a>
            </div>
        </div>
        
        <div class="product-form">
            <form>
                <div class="product-action">
                    <input type="number" placeholder="Código" name="codigo" class="input-action" required>
                    <input type="number" placeholder="Cantidad" name="cantidad" class="input-action quantity" required>
                    <button class="btn-action add">Agregar</button>
                </div>
            </form>
            <!-- La tabla para los productos -->
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Importe</th>
                        <th>Ayuda</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Productos serán agregados dinámicamente con JavaScript -->
                </tbody>
            </table>
        </div>

        <div class="footer">
            <button class="btn-action cobrar">Cobrar $0.00</button>
        </div>
    </div>
</body>
</html>
