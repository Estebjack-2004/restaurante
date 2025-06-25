<?php

namespace App\Http\Controllers;

use App\Models\conexion;
use Illuminate\Http\Request;
require_once '../models/conexion.php'; // Asegúrate de que la ruta sea correcta
class Controller
{
    
    protected $db;

    public function __construct() {
        // Establece la conexión PDO al instanciar el controlador
        $this->db = conexion::conectar();
    }

    /**
     * Método para enviar respuestas JSON estandarizadas
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Método para manejar errores comunes
     */
    protected function handleException($e) {
        error_log($e->getMessage()); // Registra el error en el log
        $this->jsonResponse([
            'success' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage()
        ], 500);
    }

    /**
     * Método para validar datos de entrada
     */
    protected function validateInput($data, $requiredFields = []) {
        foreach ($requiredFields as $field) {
            if (empty($data->$field)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => "El campo $field es requerido"
                ], 400);
            }
        }
        return true;
    }
}

