<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoModel extends Model
    {
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        public function create() {
            $query = "INSERT INTO pedidos (estado) VALUES ('pendiente')";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $this->db->lastInsertId();
        }

        public function createDetalle($pedidoId, $productoId, $cantidad) {
            $query = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad) 
                    VALUES (:pedido_id, :producto_id, :cantidad)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":pedido_id", $pedidoId);
            $stmt->bindParam(":producto_id", $productoId);
            $stmt->bindParam(":cantidad", $cantidad);
            return $stmt->execute();
        }

        public function getAllPendientes() {
            $query = "SELECT * FROM pedidos WHERE estado = 'pendiente' ORDER BY fecha ASC";
            return $this->db->query($query);
        }

        public function getDetalles($pedidoId) {
            $query = "SELECT * FROM detalles_pedido WHERE pedido_id = :pedido_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":pedido_id", $pedidoId);
            $stmt->execute();
            return $stmt;
        }

        public function updateEstado($pedidoId, $estado) {
            $query = "UPDATE pedidos SET estado = :estado WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":id", $pedidoId);
            return $stmt->execute();
        }
    }

