<?php

class productosControlador {

    public function inicio(){

        $productos = productosModelo::inicio("productos");

        $json = array(
            "Respuesta"=>$productos
        );

        echo json_encode($json, true);

        return;
    }

    public function crear(){

        $json = array(
            "Detalles"=>"Añadir productos"
        );

        echo json_encode($json, true);

        return;
    }

    public function mostrar($id){
        $json = array(
            "Detalles"=>"Este es el producto con el id => ".$id
        );

        echo json_encode($json, true);

        return;
    }

    public function editar($id){
        $json = array(
            "Detalles"=>"Producto actualizado, su id es => " . $id
        );

        echo json_encode($json, true);

        return;
    }

    public function eliminar($id){
        $json = array(
            "Detalles"=>"Producto eliminado, su id es => " . $id
        );

        echo json_encode($json, true);

        return;
    }
}