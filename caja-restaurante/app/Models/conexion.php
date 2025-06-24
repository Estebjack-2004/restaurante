<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PDO;

class conexion{

    static public function conectar(){
        $link = new PDO("mysql:host=localhost;dbname=restaurante", "root", "");
        $link -> exec("set names utf8");
        return $link;
    }
}
