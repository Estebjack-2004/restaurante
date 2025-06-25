<?php

namespace App\Http\Controllers;

use App\Models\conexion;
use App\Models\ProductoModel;
use App\Models\VentaModel;
use Exception;
use Illuminate\Http\Request;
use PDO;

class VentaController extends Controller
    {
    private $productoModel;
    private $ventaModel;

    public function __construct() {
        $this->productoModel = new ProductoModel(conexion::conectar());
        $this->ventaModel = new VentaModel(conexion::conectar());
    }

    public function registrarVenta() {
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->total) && !empty($data->detalles)) {
            try {
                $conn = conexion::conectar();
                $conn->beginTransaction();
                
                // 1. Registrar la venta
                $ventaId = $this->ventaModel->create($data->total);
                
                // 2. Registrar detalles y actualizar inventario
                foreach ($data->detalles as $detalle) {
                    $this->ventaModel->createDetalle($ventaId, $detalle->productoId, $detalle->cantidad, $detalle->precioUnitario);
                    $this->productoModel->updateCantidad($detalle->productoId, $detalle->cantidad);
                }
                
                $conn->commit();
                
                http_response_code(201);
                echo json_encode(array("message" => "Venta registrada correctamente", "venta_id" => $ventaId));
            } catch (Exception $e) {
                $conn->rollBack();
                http_response_code(503);
                echo json_encode(array("message" => "Error al registrar venta: " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos incompletos para registrar venta"));
        }
    }

    public function getVentas() {
        $stmt = $this->ventaModel->getAll();
        $ventas = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $venta = array(
                "id" => $id,
                "fecha" => $fecha,
                "total" => $total
            );
            array_push($ventas, $venta);
        }
        
        http_response_code(200);
        echo json_encode($ventas);
    }
}

