document.addEventListener("DOMContentLoaded", () => {
    // Selección de elementos del DOM
    const botonAgregar = document.querySelector(".btn-action.add");
    const inputCodigo = document.querySelector("input[name='codigo']");
    const inputCantidad = document.querySelector("input[name='cantidad']");
    const cuerpoTabla = document.querySelector("tbody");
    const botonGuardarEdicion = document.getElementById("saveEdit");
    const botonCobrar = document.querySelector(".btn-action.cobrar");
    const modalEdicionElement = document.getElementById('editModal');
    const modalEdicion = new bootstrap.Modal(modalEdicionElement, { keyboard: false });
    let filaEdicionActual = null;
    let productos = [];

    // Actualiza el total a cobrar
    const actualizarTotal = () => {
        let total = 0;
        const filas = cuerpoTabla.querySelectorAll('tr');
        filas.forEach(fila => {
            // Obtiene el importe de cada fila y lo suma al total
            const importe = parseFloat(fila.querySelector('td:nth-child(5)').textContent);
            total += importe;
        });
        // Actualiza el texto del botón de cobrar con el total calculado
        botonCobrar.textContent = `Cobrar $${total.toFixed(2)}`;
    };

    // Realiza la venta y actualiza la cantidad de productos en la base de datos
    const realizarVenta = async () => {
        const filas = cuerpoTabla.querySelectorAll('tr');
        for (const fila of filas) {
            // Obtiene el código y la cantidad vendida de cada fila
            const codigo = fila.querySelector('td:nth-child(1)').textContent;
            const cantidadVendida = parseInt(fila.querySelector('td:nth-child(4)').textContent);

            // Obtiene la información del producto desde el servidor
            const respuesta = await fetch(`../Datos/DAOProducto.php?codigo=${codigo}`);
            const producto = await respuesta.json();

            // Verifica si el producto existe
            if (!producto || producto.length === 0) {
                alert(`Producto con código ${codigo} no encontrado`);
                return false;
            }

            // Calcula la nueva cantidad después de la venta
            const nuevaCantidad = producto[0].cantidad - cantidadVendida;

            // Verifica si hay suficiente stock
            if (nuevaCantidad < 0) {
                alert(`No hay suficiente stock para el producto con código ${codigo}`);
                return false;
            }

            // Actualiza la cantidad del producto en la base de datos
            const resultado = await fetch(`../Datos/DAOProducto.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ codigo: codigo, cantidad: nuevaCantidad })
            });

            const datosResultado = await resultado.json();
            if (!datosResultado.success) {
                return false;
            }
        }
        return true;
    };

    // Evento al hacer clic en el botón Agregar
    botonAgregar.addEventListener("click", async (event) => {
        event.preventDefault();

        // Obtiene el código y la cantidad ingresados por el usuario
        const codigo = inputCodigo.value.trim();
        const cantidad = parseInt(inputCantidad.value.trim());

        // Verifica si los campos están correctamente completados
        if (codigo === "" || isNaN(cantidad) || cantidad <= 0) {
            alert("Por favor, complete los campos de Código y Cantidad correctamente.");
            return;
        }

        try {
            // Obtiene la información del producto desde el servidor
            const respuesta = await fetch(`../Datos/DAOProducto.php?codigo=${codigo}`);
            const producto = await respuesta.json();

            if (producto && producto.length > 0) {
                const productoEncontrado = producto[0];
                let cantidadTotal = cantidad;
                let productoExistente = false;

                const filas = cuerpoTabla.querySelectorAll('tr');
                filas.forEach(fila => {
                    const filaCodigo = fila.querySelector('td').textContent;
                    // Verifica si el producto ya existe en la tabla
                    if (filaCodigo === codigo) {
                        productoExistente = true;
                        const celdaCantidad = fila.querySelectorAll('td')[3];
                        cantidadTotal += parseInt(celdaCantidad.textContent);
                    }
                });

                // Verifica si la cantidad total excede el stock disponible
                if (cantidadTotal > productoEncontrado.cantidad) {
                    alert(`No puede agregar más de ${productoEncontrado.cantidad} unidades de este producto.`);
                    return;
                }

                if (productoExistente) {
                    // Si el producto ya existe, actualiza la cantidad y el importe
                    filas.forEach(fila => {
                        const filaCodigo = fila.querySelector('td').textContent;
                        if (filaCodigo === codigo) {
                            const celdaCantidad = fila.querySelectorAll('td')[3];
                            const celdaImporte = fila.querySelectorAll('td')[4];
                            const nuevaCantidad = parseInt(celdaCantidad.textContent) + cantidad;
                            celdaCantidad.textContent = nuevaCantidad;
                            celdaImporte.textContent = (productoEncontrado.precio * nuevaCantidad).toFixed(2);
                        }
                    });
                } else {
                    // Si el producto no existe, agrega una nueva fila a la tabla
                    productoEncontrado.cantidadDisponible = productoEncontrado.cantidad;
                    productoEncontrado.cantidad = cantidad;
                    productoEncontrado.importe = (productoEncontrado.precio * cantidad).toFixed(2);
                    productos.push(productoEncontrado);

                    const fila = document.createElement("tr");
                    fila.innerHTML = `
                        <td>${productoEncontrado.codigo}</td>
                        <td>${productoEncontrado.nombre}</td>
                        <td>${productoEncontrado.precio}</td>
                        <td>${productoEncontrado.cantidad}</td>
                        <td>${productoEncontrado.importe}</td>
                        <td><button class="btn-action help">Ayuda</button></td>
                        <td><button class="btn-action edit" data-bs-toggle="modal" data-bs-target="#editModal">Editar</button></td>
                        <td><button class="btn-action delete">Eliminar</button></td>
                    `;

                    cuerpoTabla.appendChild(fila);
                }

                // Limpia los campos de entrada y actualiza el total
                inputCodigo.value = "";
                inputCantidad.value = "";
                actualizarTotal();
            } else {
                alert("Producto no encontrado.");
            }
        } catch (error) {
            console.error('Error al buscar el producto:', error);
            alert("Error al buscar el producto.");
        }
    });

    // Manejo de eventos para edición y eliminación
    cuerpoTabla.addEventListener("click", (event) => {
        if (event.target.classList.contains("edit")) {
            const fila = event.target.closest("tr");
            const codigo = fila.querySelector("td:nth-child(1)").textContent;
            const nombre = fila.querySelector("td:nth-child(2)").textContent;
            const precio = fila.querySelector("td:nth-child(3)").textContent;
            const cantidad = fila.querySelector("td:nth-child(4)").textContent;

            // Carga los datos del producto en el modal de edición
            document.getElementById("editCodigo").value = codigo;
            document.getElementById("editNombre").value = nombre;
            document.getElementById("editPrecio").value = precio;
            document.getElementById("editCantidad").value = cantidad;

            filaEdicionActual = fila;
        }

        if (event.target.classList.contains("delete")) {
            const fila = event.target.closest("tr");
            const codigo = fila.querySelector("td:nth-child(1)").textContent;

            // Confirma la eliminación del producto
            if (confirm(`¿Está seguro de que desea eliminar el producto con código ${codigo}?`)) {
                cuerpoTabla.removeChild(fila);

                // Elimina el producto de la lista de productos
                productos = productos.filter(producto => producto.codigo !== codigo);
                actualizarTotal();
            }
        }
    });

    // Guardar cambios después de editar un producto
    botonGuardarEdicion.addEventListener("click", async () => {
        const codigo = document.getElementById("editCodigo").value;
        const nuevaCantidad = parseInt(document.getElementById("editCantidad").value);

        // Verifica si la nueva cantidad es válida
        if (isNaN(nuevaCantidad) || nuevaCantidad <= 0) {
            alert("Por favor, ingrese una cantidad válida.");
            return;
        }

        // Obtiene la información del producto desde el servidor
        const respuesta = await fetch(`../Datos/DAOProducto.php?codigo=${codigo}`);
        const producto = await respuesta.json();

        // Verifica si el producto existe
        if (producto && producto.length > 0) {
            const productoEncontrado = producto[0];

            // Verifica si la nueva cantidad excede el stock disponible
            if (nuevaCantidad > productoEncontrado.cantidad) {
                alert(`No puede exceder la cantidad disponible de ${productoEncontrado.cantidad}.`);
                return;
            }

            // Calcula el nuevo importe
            const importe = (productoEncontrado.precio * nuevaCantidad).toFixed(2);

            // Actualiza la fila en la tabla
            filaEdicionActual.querySelector("td:nth-child(4)").textContent = nuevaCantidad;
            filaEdicionActual.querySelector("td:nth-child(5)").textContent = importe;

            // Actualiza el producto en la lista de productos
            const productoEnLista = productos.find(p => p.codigo === codigo);
            productoEnLista.cantidad = nuevaCantidad;
            productoEnLista.importe = importe;
            actualizarTotal();

            // Cierra el modal de edición
            modalEdicion.hide();
        } else {
            alert("Producto no encontrado.");
        }
    });

    // Evento para el botón de cobrar
    botonCobrar.addEventListener("click", async () => {
        if (confirm("¿Desea realizar la venta?")) {
            const ventaExitosa = await realizarVenta();
            if (ventaExitosa) {
                cuerpoTabla.innerHTML = "";
                actualizarTotal();
                alert("Venta realizada con éxito.");
            } else {
                alert("Error al realizar la venta.");
            }
        }
    });

    // Evento que se dispara cuando se oculta el modal de edición
    modalEdicionElement.addEventListener('hidden.bs.modal', actualizarTotal);
});
