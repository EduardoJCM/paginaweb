document.addEventListener("DOMContentLoaded", () => {
    const addButton = document.querySelector(".btn-action.add");
    const codigoInput = document.querySelector("input[name='codigo']");
    const cantidadInput = document.querySelector("input[name='cantidad']");
    const tbody = document.querySelector("tbody");
    let productos = [];

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
                    // Producto ya está en la lista, actualizar cantidad e importe
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
                    // Producto no está en la lista, agregarlo
                    producto.cantidadDisponible = producto.cantidad; // Guardar cantidad disponible original
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
                        <td><button class="btn-action edit">Editar</button></td>
                        <td><button class="btn-action delete">Eliminar</button></td>
                    `;

                    tbody.appendChild(row);
                }

                codigoInput.value = "";
                cantidadInput.value = "";
            } else {
                alert("Producto no encontrado.");
            }
        } catch (error) {
            console.error('Error al buscar el producto:', error);
            alert("Error al buscar el producto.");
        }
    });
});
