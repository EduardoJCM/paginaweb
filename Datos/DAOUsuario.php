<?php
require_once __DIR__ . '/../Modelos/Usuario.php';
require_once __DIR__ . '/Conexion.php';

class DAOUsuario {
    private $conn;

    public function __construct() {
        $this->conn = (new Conexion())->conectar();
    }

    public function obtenerUsuarioPorCorreoYContrasena($correo, $contrasena) {
        $query = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashedContrasena = hash('sha256', $contrasena);
            if (hash_equals($fila['contrasenia'], $hashedContrasena)) {
                $usuario = new Usuario();
                $usuario->id = $fila['id'];
                $usuario->nombre = $fila['nombre'];
                $usuario->apellido1 = $fila['apellido1'];
                $usuario->apellido2 = $fila['apellido2'];
                $usuario->correo = $fila['correo'];
                $usuario->contrasenia = $fila['contrasenia'];
                $usuario->genero = $fila['genero'];
                $usuario->rol = $fila['rol'];
                return $usuario;
            }
        }
        return null;
    }
}
?>
