<?php
class Producto {
    public $id;
    public $codigo;
    public $nombre;
    public $precio;
    public $cantidad;

    public function __construct($id = 0, $codigo = "", $nombre = "", $precio = 0.0, $cantidad = 0) {
        $this->id = $id;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->cantidad = $cantidad;
    }
}
?>
