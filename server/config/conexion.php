<?php

class conexion{

    static public function conectar(){
        $link = new PDO("mysql:host=localhost; dbname=commerce_core", "root", "");
        $link -> exec("set names utf8");
        return $link;
    }
}