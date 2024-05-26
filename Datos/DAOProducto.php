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
}

$daoProducto = new DAOProducto();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['codigo'])) {
        $daoProducto->buscarProducto($_GET['codigo']);
    } else {
        $daoProducto->obtenerTodosLosProductos();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $result = $daoProducto->insertarProducto($data);
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
