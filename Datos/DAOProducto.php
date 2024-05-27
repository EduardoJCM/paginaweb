<?php
require_once __DIR__ . '/../Modelos/Producto.php';
require_once __DIR__ . '/Conexion.php';

class DAOProducto {
    private $conn;

    public function __construct() {
        $this->conn = (new Conexion())->conectar();
    }

    // Inserta un nuevo producto en la base de datos
    public function insertarProducto($producto) {
        // Verifica si el código del producto ya existe
        if ($this->existeCodigo($producto['codigo'])) {
            return ['success' => false, 'message' => 'Código duplicado'];
        }

        // Prepara la consulta para insertar un producto nuevo
        $query = "INSERT INTO productos (codigo, nombre, precio, cantidad) VALUES (:codigo, :nombre, :precio, :cantidad)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $producto['codigo']);
        $stmt->bindParam(':nombre', $producto['nombre']);
        $stmt->bindParam(':precio', $producto['precio']);
        $stmt->bindParam(':cantidad', $producto['cantidad']);
        return $stmt->execute() ? ['success' => true] : ['success' => false];
    }

    // Obtiene todos los productos de la base de datos
    public function obtenerProductos() {
        $query = "SELECT * FROM productos";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene productos por un código parcial
    public function obtenerProductoPorCodigo($codigo) {
        $query = "SELECT * FROM productos WHERE codigo LIKE :codigo";
        $stmt = $this->conn->prepare($query);
        $codigo = "%$codigo%";
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualiza un producto existente en la base de datos
    public function actualizarProducto($producto) {
        // Verifica si el código del producto ya existe en otro producto
        if ($this->existeCodigo($producto['codigo'], $producto['id'])) {
            return ['success' => false, 'message' => 'Código duplicado'];
        }

        // Prepara la consulta para actualizar un producto
        $query = "UPDATE productos SET nombre = :nombre, precio = :precio, cantidad = :cantidad WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $producto['id']);
        $stmt->bindParam(':nombre', $producto['nombre']);
        $stmt->bindParam(':precio', $producto['precio']);
        $stmt->bindParam(':cantidad', $producto['cantidad']);
        return $stmt->execute() ? ['success' => true] : ['success' => false];
    }

    // Elimina un producto de la base de datos
    public function eliminarProducto($id) {
        $query = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute() ? ['success' => true] : ['success' => false];
    }

    // Busca productos por un código parcial y retorna como JSON
    public function buscarProducto($codigo) {
        header('Content-Type: application/json');
        $productos = $this->obtenerProductoPorCodigo($codigo);
        echo json_encode($productos);
    }

    // Obtiene todos los productos y retorna como JSON
    public function obtenerTodosLosProductos() {
        header('Content-Type: application/json');
        $productos = $this->obtenerProductos();
        echo json_encode($productos);
    }

    // Actualiza la cantidad de un producto en la base de datos
    public function actualizarCantidadProducto($codigo, $nuevaCantidad) {
        $query = "UPDATE productos SET cantidad = :cantidad WHERE codigo = :codigo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cantidad', $nuevaCantidad);
        $stmt->bindParam(':codigo', $codigo);
        return $stmt->execute() ? ['success' => true] : ['success' => false];
    }

    // Verifica si un código de producto ya existe en la base de datos
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
    if (isset($data['codigo']) && isset($data['cantidad'])) {
        $result = $daoProducto->actualizarCantidadProducto($data['codigo'], $data['cantidad']);
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
