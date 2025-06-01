<?php

class ControladorAutorizaciones {

    //controlador mostrar autorizaciones en tabla principal
    static public function ctrMostrarAutorizaciones($item, $valor){
        $tabla = "autorizaciones";
        $respuesta = ModeloAutorizaciones::mdlMostrarAutorizaciones($tabla, $item, $valor);
        return $respuesta;
    }

}