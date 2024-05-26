<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - El Almacén de Don Sil</title>
    <link rel="stylesheet" href="css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <div class="product-action">
                <input type="text" placeholder="Código" name="codigo" class="input-action">
                <input type="number" placeholder="Cantidad" name="cantidad" class="input-action quantity">
                <button class="btn-action add">Agregar</button>
            </div>
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
                                <label for="editCodigo" class="form-label">Código</label>
                                <input type="text" class="form-control" id="editCodigo" name="codigo" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="editNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editNombre" name="nombre" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="editPrecio" class="form-label">Precio</label>
                                <input type="number" class="form-control" id="editPrecio" name="precio" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="editCantidad" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="editCantidad" name="cantidad" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="saveEdit">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</body>
</html>
