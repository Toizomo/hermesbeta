<?php

require_once "../controladores/autorizaciones.controlador.php";
require_once "../modelos/autorizaciones.modelo.php";

class AjaxAutorizaciones
{
    /*=============================================
    VARIABLES
    =============================================*/
    public $idAutorizacion;
    public $estado;
    public $id_usuario;

    /*=============================================
    MOSTRAR DETALLES DE AUTORIZACIÓN
    =============================================*/
    public function ajaxMostrarAutorizacion()
    {
        $item = "id_autorizacion";
        $valor = $this->idAutorizacion;

        $respuesta = ControladorAutorizaciones::ctrMostrarAutorizaciones($item, $valor);
        echo json_encode($respuesta);
    }

}

/*=============================================
MOSTRAR DETALLES DE AUTORIZACIÓN
=============================================*/
if (isset($_POST["idAutorizacion"]) && !isset($_POST["estado"])) {
    $autorizar = new AjaxAutorizaciones();
    $autorizar->idAutorizacion = $_POST["idAutorizacion"];
    $autorizar->ajaxMostrarAutorizacion();
}
