document.addEventListener("DOMContentLoaded", () => {
    const addButton = document.querySelector(".btn-action.add");
    const codigoInput = document.querySelector("input[name='codigo']");
    const cantidadInput = document.querySelector("input[name='cantidad']");
    const tbody = document.querySelector("tbody");
    const saveEditButton = document.getElementById("saveEdit");
    const cobrarButton = document.querySelector(".btn-action.cobrar");
    const editModalElement = document.getElementById('editModal');
    const editModal = new bootstrap.Modal(editModalElement, { keyboard: false });
    let currentEditRow = null;
    let productos = [];

    const actualizarTotal = () => {
        let total = 0;
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(row => {
            const importe = parseFloat(row.querySelector('td:nth-child(5)').textContent);
            total += importe;
        });
        cobrarButton.textContent = `Cobrar $${total.toFixed(2)}`;
    };

    const realizarVenta = async () => {
        const rows = tbody.querySelectorAll('tr');
        for (const row of rows) {
            const codigo = row.querySelector('td:nth-child(1)').textContent;
            const cantidadVendida = parseInt(row.querySelector('td:nth-child(4)').textContent);
            const response = await fetch(`../Datos/DAOProducto.php?codigo=${codigo}`);
            const producto = await response.json();
            const nuevaCantidad = producto.cantidad - cantidadVendida;

            if (nuevaCantidad < 0) {
                alert(`No hay suficiente stock para el producto con código ${codigo}`);
                return false;
            }

            const result = await fetch(`../Datos/DAOProducto.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ codigo: codigo, cantidad: nuevaCantidad })
            });

            const resultData = await result.json();
            if (!resultData.success) {
                return false;
            }
        }
        return true;
    };

    addButton.addEventListener("click", async (event) => {
        event.preventDefault();

        const codigo = codigoInput.value.trim();
        const cantidad = parseInt(cantidadInput.value.trim());

        if (codigo === "" || isNaN(cantidad) || cantidad <= 0) {
            alert("Por favor, complete los campos de Código y Cantidad correctamente.");
            return;
        }

        try {
            const response = await fetch(`../Datos/DAOProducto.php?codigo=${codigo}`);
            const producto = await response.json();

            if (producto) {
                let cantidadTotal = cantidad;
                let productoExistente = false;

                const rows = tbody.querySelectorAll('tr');
                rows.forEach(row => {
                    const rowCodigo = row.querySelector('td').textContent;
                    if (rowCodigo === codigo) {
                        productoExistente = true;
                        const cantidadCell = row.querySelectorAll('td')[3];
                        cantidadTotal += parseInt(cantidadCell.textContent);
                    }
                });

                if (cantidadTotal > producto.cantidad) {
                    alert(`No puede agregar más de ${producto.cantidad} unidades de este producto.`);
                    return;
                }

                if (productoExistente) {
                    rows.forEach(row => {
                        const rowCodigo = row.querySelector('td').textContent;
                        if (rowCodigo === codigo) {
                            const cantidadCell = row.querySelectorAll('td')[3];
                            const importeCell = row.querySelectorAll('td')[4];
                            const nuevaCantidad = parseInt(cantidadCell.textContent) + cantidad;
                            cantidadCell.textContent = nuevaCantidad;
                            importeCell.textContent = (producto.precio * nuevaCantidad).toFixed(2);
                        }
                    });
                } else {
                    producto.cantidadDisponible = producto.cantidad;
                    producto.cantidad = cantidad;
                    producto.importe = (producto.precio * cantidad).toFixed(2);
                    productos.push(producto);

                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${producto.codigo}</td>
                        <td>${producto.nombre}</td>
                        <td>${producto.precio}</td>
                        <td>${producto.cantidad}</td>
                        <td>${producto.importe}</td>
                        <td><button class="btn-action help">Ayuda</button></td>
                        <td><button class="btn-action edit" data-bs-toggle="modal" data-bs-target="#editModal">Editar</button></td>
                        <td><button class="btn-action delete">Eliminar</button></td>
                    `;

                    tbody.appendChild(row);
                }

                codigoInput.value = "";
                cantidadInput.value = "";
                actualizarTotal();
            } else {
                alert("Producto no encontrado.");
            }
        } catch (error) {
            console.error('Error al buscar el producto:', error);
            alert("Error al buscar el producto.");
        }
    });

    tbody.addEventListener("click", (event) => {
        if (event.target.classList.contains("edit")) {
            const row = event.target.closest("tr");
            const codigo = row.querySelector("td:nth-child(1)").textContent;
            const nombre = row.querySelector("td:nth-child(2)").textContent;
            const precio = row.querySelector("td:nth-child(3)").textContent;
            const cantidad = row.querySelector("td:nth-child(4)").textContent;

            document.getElementById("editCodigo").value = codigo;
            document.getElementById("editNombre").value = nombre;
            document.getElementById("editPrecio").value = precio;
            document.getElementById("editCantidad").value = cantidad;

            currentEditRow = row;
        }

        if (event.target.classList.contains("delete")) {
            const row = event.target.closest("tr");
            const codigo = row.querySelector("td:nth-child(1)").textContent;

            if (confirm(`¿Está seguro de que desea eliminar el producto con código ${codigo}?`)) {
                tbody.removeChild(row);

                productos = productos.filter(producto => producto.codigo !== codigo);
                actualizarTotal();
            }
        }
    });

    saveEditButton.addEventListener("click", async () => {
        const codigo = document.getElementById("editCodigo").value;
        const nuevaCantidad = parseInt(document.getElementById("editCantidad").value);

        if (isNaN(nuevaCantidad) || nuevaCantidad <= 0) {
            alert("Por favor, ingrese una cantidad válida.");
            return;
        }

        const response = await fetch(`../Datos/DAOProducto.php?codigo=${codigo}`);
        const producto = await response.json();

        if (nuevaCantidad > producto.cantidad) {
            alert(`No puede exceder la cantidad disponible de ${producto.cantidad}.`);
            return;
        }

        const importe = (producto.precio * nuevaCantidad).toFixed(2);

        currentEditRow.querySelector("td:nth-child(4)").textContent = nuevaCantidad;
        currentEditRow.querySelector("td:nth-child(5)").textContent = importe;

        const productoEnLista = productos.find(p => p.codigo === codigo);
        productoEnLista.cantidad = nuevaCantidad;
        productoEnLista.importe = importe;
        actualizarTotal();

        editModal.hide();
    });

    cobrarButton.addEventListener("click", async () => {
        if (confirm("¿Desea realizar la venta?")) {
            const ventaExitosa = await realizarVenta();
            if (ventaExitosa) {
                tbody.innerHTML = "";
                actualizarTotal();
                alert("Venta realizada con éxito.");
            } else {
                alert("Error al realizar la venta.");
            }
        }
    });

    editModalElement.addEventListener('hidden.bs.modal', actualizarTotal);
});
