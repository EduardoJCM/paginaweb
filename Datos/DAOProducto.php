<?php
require_once __DIR__ . '/../Modelos/Producto.php';
require_once __DIR__ . '/Conexion.php';

class DAOProducto {
    private $conn;

    public function __construct() {
        $this->conn = (new Conexion())->conectar();
    }

    public function insertarProducto($producto) {
        if ($this->existeCodigo($producto['codigo'])) {
            return ['success' => false, 'message' => 'Código duplicado'];
        }

        $query = "INSERT INTO productos (codigo, nombre, precio, cantidad) VALUES (:codigo, :nombre, :precio, :cantidad)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $producto['codigo']);
        $stmt->bindParam(':nombre', $producto['nombre']);
        $stmt->bindParam(':precio', $producto['precio']);
        $stmt->bindParam(':cantidad', $producto['cantidad']);
        return $stmt->execute() ? ['success' => true] : ['success' => false];
    }

    public function obtenerProductos() {
        $query = "SELECT * FROM productos";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductoPorCodigo($codigo) {
        $query = "SELECT * FROM productos WHERE codigo LIKE :codigo";
        $stmt = $this->conn->prepare($query);
        $codigo = "%$codigo%";
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarProducto($producto) {
        if ($this->existeCodigo($producto['codigo'], $producto['id'])) {
            return ['success' => false, 'message' => 'Código duplicado'];
        }

        $query = "UPDATE productos SET nombre = :nombre, precio = :precio, cantidad = :cantidad WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $producto['id']);
        $stmt->bindParam(':nombre', $producto['nombre']);
        $stmt->bindParam(':precio', $producto['precio']);
        $stmt->bindParam(':cantidad', $producto['cantidad']);
        return $stmt->execute() ? ['success' => true] : ['success' => false];
    }

    public function eliminarProducto($id) {
        $query = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute() ? ['success' => true] : ['success' => false];
    }

    public function buscarProducto($codigo) {
        header('Content-Type: application/json');
        $productos = $this->obtenerProductoPorCodigo($codigo);
        echo json_encode($productos);
    }

    public function obtenerTodosLosProductos() {
        header('Content-Type: application/json');
        $productos = $this->obtenerProductos();
        echo json_encode($productos);
    }

    public function actualizarCantidadProducto($codigo, $nuevaCantidad) {
        $query = "UPDATE productos SET cantidad = :cantidad WHERE codigo = :codigo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cantidad', $nuevaCantidad);
        $stmt->bindParam(':codigo', $codigo);
        return $stmt->execute() ? ['success' => true] : ['success' => false];
    }

    private function existeCodigo($codigo, $id = null) {
        $query = "SELECT * FROM productos WHERE codigo = :codigo";
        if ($id) {
            $query .= " AND id != :id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        if ($id) {
            $stmt->bindParam(':id', $id);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function registrarVentaCompleta($productosVenta, $totalVenta) {
        try {
            $this->conn->beginTransaction();

            // Registrar venta
            $queryVenta = "INSERT INTO ventas (total) VALUES (:total)";
            $stmtVenta = $this->conn->prepare($queryVenta);
            $stmtVenta->bindParam(':total', $totalVenta);
            $stmtVenta->execute();
            $ventaId = $this->conn->lastInsertId();

            // Registrar detalles de la venta
            $queryDetalle = "INSERT INTO detalle_ventas (venta_id, producto_id, nombre_producto, cantidad, precio, total) 
                             VALUES (:venta_id, :producto_id, :nombre_producto, :cantidad, :precio, :total)";
            $stmtDetalle = $this->conn->prepare($queryDetalle);

            foreach ($productosVenta as $producto) {
                $stmtDetalle->bindParam(':venta_id', $ventaId);
                $stmtDetalle->bindParam(':producto_id', $producto['producto_id']);
                $stmtDetalle->bindParam(':nombre_producto', $producto['nombre_producto']);
                $stmtDetalle->bindParam(':cantidad', $producto['cantidad']);
                $stmtDetalle->bindParam(':precio', $producto['precio']);
                $stmtDetalle->bindParam(':total', $producto['total']);
                $stmtDetalle->execute();

                // Actualizar cantidad del producto
                $queryActualizar = "UPDATE productos SET cantidad = cantidad - :cantidad WHERE id = :producto_id";
                $stmtActualizar = $this->conn->prepare($queryActualizar);
                $stmtActualizar->bindParam(':cantidad', $producto['cantidad']);
                $stmtActualizar->bindParam(':producto_id', $producto['producto_id']);
                $stmtActualizar->execute();
            }

            $this->conn->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

// Manejo de solicitudes HTTP
$daoProducto = new DAOProducto();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['codigo'])) {
        $daoProducto->buscarProducto($_GET['codigo']);
    } else {
        $daoProducto->obtenerTodosLosProductos();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['productos']) && isset($data['total'])) {
        $result = $daoProducto->registrarVentaCompleta($data['productos'], $data['total']);
    } else {
        $result = $daoProducto->insertarProducto($data);
    }
    echo json_encode($result);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $result = $daoProducto->actualizarProducto($data);
    echo json_encode($result);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    $result = $daoProducto->eliminarProducto($id);
    echo json_encode($result);
}
?>
