<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoModel extends Model
    {

        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        public function getAllProductos() {
            $query = "SELECT * FROM productos";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        public function getProducto($id) {
            $query = "SELECT * FROM productos WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            return $stmt;
        }

        public function updateCantidad($id, $cantidad) {
            $query = "UPDATE productos SET cantidad = cantidad - ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $cantidad);
            $stmt->bindParam(2, $id);
            return $stmt->execute();
        }
    }
