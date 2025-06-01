<?php

require_once "conexion.php";

class ModeloAutorizaciones {

    // modelo para mostrar autorizaciones
    static public function mdlMostrarAutorizaciones($tabla, $item, $valor){
        if($item != null){
            $stmt = Conexion::conectar()->prepare("SELECT a.*, p.fecha_inicio, p.fecha_fin, p.fecha_solicitud, p.estado_prestamo 
                FROM autorizaciones a 
                INNER JOIN prestamos p ON a.id_prestamo = p.id_prestamo 
                WHERE a.$item = :$item");
            $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        }else{
            $stmt = Conexion::conectar()->prepare("SELECT a.*, p.fecha_inicio, p.fecha_fin, p.fecha_solicitud, p.estado_prestamo 
                FROM autorizaciones a 
                INNER JOIN prestamos p ON a.id_prestamo = p.id_prestamo");
            $stmt->execute();
            return $stmt->fetchAll();
        }
        $stmt->close();
        $stmt = null;
    }

}