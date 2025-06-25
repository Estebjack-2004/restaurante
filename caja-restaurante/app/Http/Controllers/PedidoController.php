<?php

namespace App\Http\Controllers;

use App\Models\conexion;
use App\Models\PedidoModel;
use App\Models\ProductoModel;
use Exception;
use Illuminate\Http\Request;
use PDO;

class PedidoController extends Controller
{
    
    private $pedidoModel;
    private $productoModel;

    public function __construct() {
        $this->pedidoModel = new PedidoModel(conexion::conectar());
        $this->productoModel = new ProductoModel(conexion::conectar());
    }

    public function crearPedido() {
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->detalles)) {
            try {
                $conn = conexion::conectar();
                $conn->beginTransaction();
                
                // 1. Crear el pedido
                $pedidoId = $this->pedidoModel->create();
                
                // 2. Agregar detalles del pedido
                foreach ($data->detalles as $detalle) {
                    $this->pedidoModel->createDetalle($pedidoId, $detalle->productoId, $detalle->cantidad);
                }
                
                $conn->commit();
                
                http_response_code(201);
                echo json_encode(array("message" => "Pedido creado correctamente", "pedido_id" => $pedidoId));
            } catch (Exception $e) {
                $conn->rollBack();
                http_response_code(503);
                echo json_encode(array("message" => "Error al crear pedido: " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos incompletos para crear pedido"));
        }
    }

    public function getPedidosPendientes() {
        $stmt = $this->pedidoModel->getAllPendientes();
        $pedidos = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $pedido = array(
                "id" => $id,
                "fecha" => $fecha,
                "estado" => $estado,
                "detalles" => array()
            );
            
            // Obtener detalles del pedido
            $detallesStmt = $this->pedidoModel->getDetalles($id);
            while ($detalle = $detallesStmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($pedido["detalles"], array(
                    "productoId" => $detalle["producto_id"],
                    "cantidad" => $detalle["cantidad"]
                ));
            }
            
            array_push($pedidos, $pedido);
        }
        
        http_response_code(200);
        echo json_encode($pedidos);
    }

    public function procesarPedido($id) {
        try {
            $conn = conexion::conectar();
            $conn->beginTransaction();
            
            // 1. Cambiar estado del pedido
            $this->pedidoModel->updateEstado($id, 'procesado');
            
            $conn->commit();
            
            http_response_code(200);
            echo json_encode(array("message" => "Pedido procesado correctamente"));
        } catch (Exception $e) {
            $conn->rollBack();
            http_response_code(503);
            echo json_encode(array("message" => "Error al procesar pedido: " . $e->getMessage()));
        }
    }
}

