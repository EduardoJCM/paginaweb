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
                <a href="index.html" >Inicio</a>
                <a href="gestion-productos.html" class="active">Gestión de Productos</a>
                <a href="ventas.html">Ventas</a>
                <a href="registro.html">Registro</a>
            </div>
            <div class="navbar-right">
                <a href="login.html" class="logout">Cerrar Sesión</a>
            </div>
        </div>
        <div class="product-form">
            <div class="product-action">
                <input type="text" placeholder="Buscar por código" name="search" class="input-action">
                <button class="btn-action search">Buscar</button>
                <a href="agregar.html"><button class="btn-action add">Agregar</button></a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Cant. Mín. Mayoreo</th>
                        <th>Precio Mayoreo</th>
                        <th>Existencias</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>0001</td>
                        <td>Producto Ejemplo</td>
                        <td>$10.00</td>
                        
                        <td>10</td>
                        <td>$7.00</td>
                        <td>20</td>
                        <td><button class="btn-action edit">Editar</button></td>
                        <td><button class="btn-action delete">Eliminar</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
