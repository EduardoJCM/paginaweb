document.addEventListener("DOMContentLoaded", () => {
    // Selección de elementos del DOM
    const cuerpoTabla = document.querySelector("tbody");
    const botonBuscar = document.querySelector(".btn-action.search");
    const inputBuscar = document.querySelector("input[name='search']");
    const botonGuardarEdicion = document.getElementById("guardarEdicion");
    const modalEdicionElement = document.getElementById('editModal');
    const modalEdicion = new bootstrap.Modal(modalEdicionElement, { keyboard: false });
    const botonResetearBusqueda = document.createElement("button");
    botonResetearBusqueda.textContent = "Resetear Búsqueda";
    botonResetearBusqueda.classList.add("btn-action", "reset");
    document.querySelector(".product-action").appendChild(botonResetearBusqueda);
    let productos = [];
    let productoActual = null;
    let modoEdicion = false;

    // Carga los productos desde la base de datos
    const cargarProductos = async (codigo = "") => {
        try {
            // Determina la URL de la solicitud en función de si se ha proporcionado un código
            const url = codigo ? `../Datos/DAOProducto.php?codigo=${codigo}` : '../Datos/DAOProducto.php';
            const respuesta = await fetch(url);
            productos = await respuesta.json();
            mostrarProductos(productos);
        } catch (error) {
            console.error('Error al cargar los productos:', error);
        }
    };

    // Muestra los productos en la tabla
    const mostrarProductos = (productos) => {
        cuerpoTabla.innerHTML = "";
        productos.forEach(producto => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${producto.id}</td>
                <td>${producto.codigo}</td>
                <td>${producto.nombre}</td>
                <td>${producto.precio}</td>
                <td>${producto.cantidad}</td>
                <td><button class="btn-action edit" data-id="${producto.id}">Editar</button></td>
                <td><button class="btn-action delete" data-id="${producto.id}">Eliminar</button></td>
            `;
            cuerpoTabla.appendChild(fila);
        });
    };

    // Busca un producto por su código
    const buscarProducto = () => {
        const codigo = inputBuscar.value.trim();
        if (codigo === "") {
            alert("Por favor, ingrese un código para buscar.");
            return;
        }
        cargarProductos(codigo);
    };

    // Resetea la búsqueda y carga todos los productos
    const resetearBusqueda = () => {
        inputBuscar.value = "";
        cargarProductos();
    };

    // Carga la información del producto en el formulario para editar
    const cargarProductoEnFormulario = (producto) => {
        document.getElementById("editId").value = producto.id || "";
        document.getElementById("editCodigo").value = producto.codigo || "";
        document.getElementById("editNombre").value = producto.nombre || "";
        document.getElementById("editPrecio").value = producto.precio || "";
        document.getElementById("editCantidad").value = producto.cantidad || "";
        productoActual = producto;
    };

    // Valida que no haya duplicados de código
    const validarDuplicados = (codigo, id) => {
        return productos.some(producto => producto.codigo === codigo && (id ? producto.id != id : true));
    };

    // Guarda la edición del producto
    const guardarEdicionProducto = async () => {
        const id = document.getElementById("editId").value;
        const codigo = document.getElementById("editCodigo").value.trim();
        const nombre = document.getElementById("editNombre").value.trim();
        const precio = parseFloat(document.getElementById("editPrecio").value.trim());
        const cantidad = parseInt(document.getElementById("editCantidad").value.trim());

        // Verifica si los campos están correctamente completados
        if (codigo === "" || nombre === "" || isNaN(precio) || isNaN(cantidad) || cantidad < 0 || precio < 0) {
            alert("Por favor, complete los campos de Código, Nombre, Precio y Cantidad correctamente.");
            return;
        }

        // Verifica si el código del producto ya existe
        if (validarDuplicados(codigo, id)) {
            alert("El código ya existe. Por favor, use un código diferente.");
            return;
        }

        // Confirma la acción del usuario
        if (!confirm("¿Desea guardar los cambios en este producto?")) {
            return;
        }

        const productoActualizado = { ...productoActual, id, codigo, nombre, precio, cantidad };

        try {
            let respuesta;
            // Si está en modo edición, actualiza el producto
            if (modoEdicion) {
                respuesta = await fetch(`../Datos/DAOProducto.php`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(productoActualizado)
                });
            } else {
                // Si no está en modo edición, crea un nuevo producto
                respuesta = await fetch(`../Datos/DAOProducto.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(productoActualizado)
                });
            }

            const resultado = await respuesta.json();
            if (resultado.success) {
                cargarProductos();
                modalEdicion.hide();
            } else if (resultado.message === 'Código duplicado') {
                alert("El código ya existe. Por favor, use un código diferente.");
            } else {
                alert("Error al guardar el producto.");
            }
        } catch (error) {
            console.error('Error al guardar el producto:', error);
        }
    };

    // Elimina un producto de la base de datos
    const eliminarProducto = async (id) => {
        if (!confirm("¿Está seguro de que desea eliminar este producto?")) {
            return;
        }

        try {
            const respuesta = await fetch(`../Datos/DAOProducto.php`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });
            const resultado = await respuesta.json();
            if (resultado.success) {
                cargarProductos();
            } else {
                alert("Error al eliminar el producto.");
            }
        } catch (error) {
            console.error('Error al eliminar el producto:', error);
        }
    };

    // Manejo de eventos de edición y eliminación en la tabla
    cuerpoTabla.addEventListener("click", (event) => {
        if (event.target.classList.contains("edit")) {
            const id = event.target.getAttribute("data-id");
            const producto = productos.find(p => p.id == id);
            cargarProductoEnFormulario(producto);
            modoEdicion = true;
            modalEdicion.show();
        }

        if (event.target.classList.contains("delete")) {
            const id = event.target.getAttribute("data-id");
            eliminarProducto(id);
        }
    });

    // Evento para el botón de agregar producto
    document.querySelector(".btn-action.add").addEventListener("click", () => {
        cargarProductoEnFormulario({});
        modoEdicion = false;
        modalEdicion.show();
    });

    // Evento para el botón de buscar producto
    botonBuscar.addEventListener("click", buscarProducto);

    // Evento para el botón de resetear búsqueda
    botonResetearBusqueda.addEventListener("click", resetearBusqueda);

    // Evento para guardar la edición del producto
    botonGuardarEdicion.addEventListener("click", guardarEdicionProducto);

    // Carga inicial de productos al cargar la página
    cargarProductos();
});
