document.addEventListener("DOMContentLoaded", () => {
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

    const actualizarTotal = () => {
        let total = 0;
        const filas = cuerpoTabla.querySelectorAll('tr');
        filas.forEach(fila => {
            const importe = parseFloat(fila.querySelector('td:nth-child(5)').textContent);
            total += importe;
        });
        botonCobrar.textContent = `Cobrar $${total.toFixed(2)}`;
    };

    const realizarVenta = async () => {
        const filas = cuerpoTabla.querySelectorAll('tr');
        for (const fila of filas) {
            const codigo = fila.querySelector('td:nth-child(1)').textContent;
            const cantidadVendida = parseInt(fila.querySelector('td:nth-child(4)').textContent);
            const respuesta = await fetch(`../Datos/DAOProducto.php?codigo=${codigo}`);
            const producto = await respuesta.json();
            const nuevaCantidad = producto.cantidad - cantidadVendida;

            if (nuevaCantidad < 0) {
                alert(`No hay suficiente stock para el producto con código ${codigo}`);
                return false;
            }

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

    botonAgregar.addEventListener("click", async (event) => {
        event.preventDefault();

        const codigo = inputCodigo.value.trim();
        const cantidad = parseInt(inputCantidad.value.trim());

        if (codigo === "" || isNaN(cantidad) || cantidad <= 0) {
            alert("Por favor, complete los campos de Código y Cantidad correctamente.");
            return;
        }

        try {
            const respuesta = await fetch(`../Datos/DAOProducto.php?codigo=${codigo}`);
            const producto = await respuesta.json();

            if (producto) {
                let cantidadTotal = cantidad;
                let productoExistente = false;

                const filas = cuerpoTabla.querySelectorAll('tr');
                filas.forEach(fila => {
                    const filaCodigo = fila.querySelector('td').textContent;
                    if (filaCodigo === codigo) {
                        productoExistente = true;
                        const celdaCantidad = fila.querySelectorAll('td')[3];
                        cantidadTotal += parseInt(celdaCantidad.textContent);
                    }
                });

                if (cantidadTotal > producto.cantidad) {
                    alert(`No puede agregar más de ${producto.cantidad} unidades de este producto.`);
                    return;
                }

                if (productoExistente) {
                    filas.forEach(fila => {
                        const filaCodigo = fila.querySelector('td').textContent;
                        if (filaCodigo === codigo) {
                            const celdaCantidad = fila.querySelectorAll('td')[3];
                            const celdaImporte = fila.querySelectorAll('td')[4];
                            const nuevaCantidad = parseInt(celdaCantidad.textContent) + cantidad;
                            celdaCantidad.textContent = nuevaCantidad;
                            celdaImporte.textContent = (producto.precio * nuevaCantidad).toFixed(2);
                        }
                    });
                } else {
                    producto.cantidadDisponible = producto.cantidad;
                    producto.cantidad = cantidad;
                    producto.importe = (producto.precio * cantidad).toFixed(2);
                    productos.push(producto);

                    const fila = document.createElement("tr");
                    fila.innerHTML = `
                        <td>${producto.codigo}</td>
                        <td>${producto.nombre}</td>
                        <td>${producto.precio}</td>
                        <td>${producto.cantidad}</td>
                        <td>${producto.importe}</td>
                        <td><button class="btn-action help">Ayuda</button></td>
                        <td><button class="btn-action edit" data-bs-toggle="modal" data-bs-target="#editModal">Editar</button></td>
                        <td><button class="btn-action delete">Eliminar</button></td>
                    `;

                    cuerpoTabla.appendChild(fila);
                }

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

    cuerpoTabla.addEventListener("click", (event) => {
        if (event.target.classList.contains("edit")) {
            const fila = event.target.closest("tr");
            const codigo = fila.querySelector("td:nth-child(1)").textContent;
            const nombre = fila.querySelector("td:nth-child(2)").textContent;
            const precio = fila.querySelector("td:nth-child(3)").textContent;
            const cantidad = fila.querySelector("td:nth-child(4)").textContent;

            document.getElementById("editCodigo").value = codigo;
            document.getElementById("editNombre").value = nombre;
            document.getElementById("editPrecio").value = precio;
            document.getElementById("editCantidad").value = cantidad;

            filaEdicionActual = fila;
        }

        if (event.target.classList.contains("delete")) {
            const fila = event.target.closest("tr");
            const codigo = fila.querySelector("td:nth-child(1)").textContent;

            if (confirm(`¿Está seguro de que desea eliminar el producto con código ${codigo}?`)) {
                cuerpoTabla.removeChild(fila);

                productos = productos.filter(producto => producto.codigo !== codigo);
                actualizarTotal();
            }
        }
    });

    botonGuardarEdicion.addEventListener("click", async () => {
        const codigo = document.getElementById("editCodigo").value;
        const nuevaCantidad = parseInt(document.getElementById("editCantidad").value);

        if (isNaN(nuevaCantidad) || nuevaCantidad <= 0) {
            alert("Por favor, ingrese una cantidad válida.");
            return;
        }

        const respuesta = await fetch(`../Datos/DAOProducto.php?codigo=${codigo}`);
        const producto = await respuesta.json();

        if (nuevaCantidad > producto.cantidad) {
            alert(`No puede exceder la cantidad disponible de ${producto.cantidad}.`);
            return;
        }

        const importe = (producto.precio * nuevaCantidad).toFixed(2);

        filaEdicionActual.querySelector("td:nth-child(4)").textContent = nuevaCantidad;
        filaEdicionActual.querySelector("td:nth-child(5)").textContent = importe;

        const productoEnLista = productos.find(p => p.codigo === codigo);
        productoEnLista.cantidad = nuevaCantidad;
        productoEnLista.importe = importe;
        actualizarTotal();

        modalEdicion.hide();
    });

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

    modalEdicionElement.addEventListener('hidden.bs.modal', actualizarTotal);
});
