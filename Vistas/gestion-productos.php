<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - El Almacén de Don Sil</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="content">
        <div class="navbar">
            <div class="navbar-left">
                <a href="index.php" >Inicio</a>
                <a href="gestion-productos.php" class="active">Gestión de Productos</a>
                <a href="ventas.php">Ventas</a>
                <a href="registro.php">Registro</a>
            </div>
            <div class="navbar-right">
                <a href="login.php" class="logout">Cerrar Sesión</a>
            </div>
        </div>
        <div class="product-form">
            <div class="product-action">
                <input type="text" placeholder="Buscar por código" name="search" class="input-action">
                <button class="btn-action search">Buscar</button>
                <a href="agregar.php"><button class="btn-action add">Agregar</button></a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Existencias</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>1234567890123</td>
                        <td>Coca Cola 3L</td>        
                        <td>35.00</td>
                        <td>48</td>
                        <td><button class="btn-action edit">Editar</button></td>
                        <td><button class="btn-action delete">Eliminar</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
