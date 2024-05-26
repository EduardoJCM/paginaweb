<?php
require_once __DIR__ . '/../Modelos/Producto.php';
require_once __DIR__ . '/Conexion.php';

class DAOProducto {
    private $conn;

    public function __construct() {
        $this->conn = (new Conexion())->conectar();
    }

    public function insertarProducto($producto) {
        $query = "INSERT INTO productos (codigo, nombre, precio, cantidad) VALUES (:codigo, :nombre, :precio, :cantidad)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $producto->codigo);
        $stmt->bindParam(':nombre', $producto->nombre);
        $stmt->bindParam(':precio', $producto->precio);
        $stmt->bindParam(':cantidad', $producto->cantidad);
        return $stmt->execute();
    }

    public function obtenerProductos() {
        $query = "SELECT * FROM productos";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductoPorCodigo($codigo) {
        $query = "SELECT * FROM productos WHERE codigo = :codigo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarCantidadProducto($codigo, $cantidad) {
        $query = "UPDATE productos SET cantidad = :cantidad WHERE codigo = :codigo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':cantidad', $cantidad);
        return $stmt->execute();
    }

    public function buscarProducto($codigo) {
        header('Content-Type: application/json');
        $producto = $this->obtenerProductoPorCodigo($codigo);
        echo json_encode($producto);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['codigo'])) {
    $daoProducto = new DAOProducto();
    $daoProducto->buscarProducto($_GET['codigo']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $daoProducto = new DAOProducto();
    $codigo = $data['codigo'];
    $cantidad = $data['cantidad'];
    $result = $daoProducto->actualizarCantidadProducto($codigo, $cantidad);
    echo json_encode(['success' => $result]);
}
?>
