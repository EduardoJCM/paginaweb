<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - El Almacén de Don Sil</title>
    <link rel="stylesheet" href="css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="js/gestion-productos.js" defer></script>
</head>
<body>
    <div class="content">
        <div class="navbar">
            <div class="navbar-left">
                <a href="index.php">Inicio</a>
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
                <button class="btn-action add" data-bs-toggle="modal" data-bs-target="#editModal">Agregar</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Existencias</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Productos serán agregados dinámicamente con JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para editar producto -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="mb-3">
                            <label for="editId" class="form-label">ID</label>
                            <input type="text" class="form-control" id="editId" name="id" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editCodigo" class="form-label">Código</label>
                            <input type="text" class="form-control" id="editCodigo" name="codigo" required>
                        </div>
                        <div class="mb-3">
                            <label for="editNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPrecio" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="editPrecio" name="precio" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="editCantidad" name="cantidad" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarEdicion">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
