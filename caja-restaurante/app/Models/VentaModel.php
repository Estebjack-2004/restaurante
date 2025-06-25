<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaModel extends Model
    {
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        public function create($total) {
            $query = "INSERT INTO ventas (total) VALUES (:total)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":total", $total);
            $stmt->execute();
            return $this->db->lastInsertId();
        }

        public function createDetalle($ventaId, $productoId, $cantidad, $precioUnitario) {
            $query = "INSERT INTO detalles_venta (venta_id, producto_id, cantidad, precio_unitario) 
                    VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":venta_id", $ventaId);
            $stmt->bindParam(":producto_id", $productoId);
            $stmt->bindParam(":cantidad", $cantidad);
            $stmt->bindParam(":precio_unitario", $precioUnitario);
            return $stmt->execute();
        }

        public function getAll() {
            $query = "SELECT * FROM ventas ORDER BY fecha DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        }
    }