<?php

namespace App\Http\Controllers;

use App\Models\conexion;
use App\Models\ProductoModel;
use Illuminate\Http\Request;
use PDO;

class ProductoController extends Controller
{
    
    private $productoModel;

    public function __construct() {
        $this->productoModel = new ProductoModel(conexion::conectar());
    }

    public function getAll() {
        $stmt = $this->productoModel->getAllProductos();
        $productos = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $producto = array(
                "id" => $id,
                "nombre" => $nombre,
                "precio" => $precio,
                "cantidad" => $cantidad
            );
            array_push($productos, $producto);
        }
        http_response_code(200);
        echo json_encode($productos);
    }
}

