<?php

class ControladorUsuarios{

    public function ctrIngresoUsuario(){
        if (isset($_POST["ingUsuario"])) {
            if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingUsuario"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])) {
                
                $encriptar = crypt($_POST["ingPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
                // echo $encriptar;

                $tabla = "usuarios";
                $item = "nombre_usuario";
                $valor = $_POST["ingUsuario"];

                $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
                if (is_array($respuesta)) {

                if ($respuesta["nombre_usuario"] == $_POST["ingUsuario"] && $respuesta["clave"] == $encriptar) 
                {
                    if($respuesta["estado"] == "activo") {
                        // Iniciar sesión y guardar datos del usuario
                        $_SESSION["iniciarSesion"] = "ok";
                        $_SESSION["id_usuario"] = $respuesta["id_usuario"];
                        $_SESSION["nombre"] = $respuesta["nombre"];
                        $_SESSION["apellido"] = $respuesta["apellido"];
                        $_SESSION["usuario"] = $respuesta["nombre_usuario"];
                        $_SESSION["foto"] = $respuesta["foto"];
                        $_SESSION["rol"] = $respuesta["id_rol"];
                        $_SESSION["nombre_rol"] = $respuesta["nombre_rol"];

                        // Obtener permisos del rol
                        $permisos = ModeloPermisos::mdlMostrarPermisos("id_rol", $respuesta["id_rol"]);
                        $_SESSION["permisos"] = array();
                        foreach($permisos as $permiso) {
                            $_SESSION["permisos"][] = $permiso["id_permiso"];
                        }

                        echo '<script>
                            window.location = "inicio";
                        </script>';
                    } else {
                        echo '<br><div class="alert alert-danger">El usuario está inactivo</div>';
                    }
                } else {
                    echo '<br><div class="alert alert-danger">Usuario y/o contraseña incorrectos</div>';
                }
            }
        }
    }
}

    static public function ctrEditarPerfil() {
        if (isset($_POST["editarEmail"])) {
            
            /*=============================================
            OBTENER USUARIO Y NUMERO DE DOCUMENTO
            =============================================*/
            $usuario = self::ctrMostrarUsuarios("id_usuario", $_POST["idUsuario"]);
            $numeroDocumento = $usuario["numero_documento"];
            
            /*=============================================
            VALIDAR IMAGEN
            =============================================*/
            $ruta = $usuario["foto"]; // Mantener foto actual
    
            if (isset($_FILES["editarFoto"]["tmp_name"]) && !empty($_FILES["editarFoto"]["tmp_name"])) {
                
                /*=============================================
                VALIDAR TIPO DE ARCHIVO
                =============================================*/
                $allowed = array('jpg', 'jpeg', 'png');
                $fileType = strtolower(pathinfo($_FILES["editarFoto"]["name"], PATHINFO_EXTENSION));
                
                if (!in_array($fileType, $allowed)) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Solo se permiten archivos JPG y PNG",
                            confirmButtonText: "Cerrar"
                        });
                    </script>';
                    return;
                }
    
                /*=============================================
                CREAR DIRECTORIO CON NÚMERO DE DOCUMENTO
                =============================================*/
                $directorio = "vistas/img/usuarios/".$numeroDocumento;
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }
    
                /*=============================================
                GENERAR NOMBRE ÚNICO
                =============================================*/
                $aleatorio = mt_rand(100, 999);
                $ruta = $directorio."/".$aleatorio.".".$fileType;
    
                /*=============================================
                MOVER ARCHIVO
                =============================================*/
                if (move_uploaded_file($_FILES["editarFoto"]["tmp_name"], $ruta)) {
                    /*=============================================
                    BORRAR FOTO ANTERIOR
                    =============================================*/
                    if (!empty($usuario["foto"]) && 
                        $usuario["foto"] != "vistas/img/usuarios/default/anonymous.png" &&
                        file_exists($usuario["foto"])) {
                        unlink($usuario["foto"]);
                    }
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "No se pudo subir la imagen",
                            confirmButtonText: "Cerrar"
                        });
                    </script>';
                    return;
                }
            }
    
            /*=============================================
            ACTUALIZAR BASE DE DATOS
            =============================================*/
            $tabla = "usuarios";
            $datos = array(
                "id_usuario" => $_POST["idUsuario"],
                "correo_electronico" => $_POST["editarEmail"],
                "telefono" => $_POST["editarTelefono"],
                "direccion" => $_POST["editarDireccion"],
                "genero" => $_POST["editarGenero"],
                "foto" => $ruta
            );
    
            $respuesta = ModeloUsuarios::mdlEditarPerfil($tabla, $datos);
    
            if ($respuesta == "ok") {
                $_SESSION["foto"] = $ruta;
                
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Perfil actualizado!",
                        text: "Los cambios se guardaron correctamente",
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if (result.value) {
                            window.location = "inicio";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudo actualizar el perfil",
                        confirmButtonText: "Cerrar"
                    });
                </script>';
            }
        }
    }
    

    static public function ctrCrearUsuario(){
        
        if (isset($_POST["nuevoNombre"]) &&
            isset($_POST["nuevoApellido"]) &&
            isset($_POST["nuevoTipoDocumento"]) &&
            isset($_POST["nuevoNumeroDocumento"]) ){
            if (preg_match('/^[a-zA-ZñÑáéíóÁÉÍÓÚ ]+$/', $_POST["nuevoNombre"]) &&
                preg_match('/^[a-zA-ZñÑáéíóÁÉÍÓÚ ]+$/', $_POST["nuevoApellido"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["nuevoNumeroDocumento"]) ) {
                // si el usuario es aprendiz se debe validar la sede y la ficha
                if ($_POST["selectRol"] != 6) {
                    $sede = "";
                    $ficha = "";
                }else{
                    $sede = $_POST["id_sede"];
                    $ficha = $_POST["id_ficha"];
                }

                $directorio = "vistas/img/usuarios/".$_POST["nuevoNumeroDocumento"];
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                // Imagen por defecto
                $ruta = "vistas/img/usuarios/default/anonymous.png";

                // Si se sube una imagen
                if (isset($_FILES["nuevaFoto"]["tmp_name"]) && !empty($_FILES["nuevaFoto"]["tmp_name"])) {
                    list($ancho, $alto) = getimagesize($_FILES["nuevaFoto"]["tmp_name"]);
                    
                    $nuevoAncho = 500;
                    $nuevoAlto = 500;
                    
                    $aleatorio = mt_rand(100, 999);
                    $ruta = $directorio."/".$aleatorio.".jpg";
                    
                    $origen = imagecreatefromjpeg($_FILES["nuevaFoto"]["tmp_name"]);
                    $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                    imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                    imagejpeg($destino, $ruta);
                }
                $tabla = "usuarios";
                $encriptar = crypt($_POST["nuevoPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
                $datos = array(
                    "nombre" => $_POST["nuevoNombre"],
                    "apellido" => $_POST["nuevoApellido"],
                    "tipo_documento" => $_POST["nuevoTipoDocumento"],
                    "documento" => $_POST["nuevoNumeroDocumento"],
                    "email" => $_POST["nuevoEmail"],
                    "telefono" => $_POST["nuevoTelefono"],
                    "direccion" => $_POST["nuevaDireccion"],
                    "genero" => $_POST["nuevoGenero"],
                    "usuario" => $_POST["nuevoNumeroDocumento"],
                    "password" => $encriptar,
                    "rol" => $_POST["selectRol"],
                    "foto" => $ruta,
                    // si es aprendiz
                    "sede" => $sede,
                    "ficha" => $ficha
                );

                $respuesta = ModeloUsuarios::mdlCrearUsuario($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡El usuario ha sido creado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "usuarios";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error al crear el usuario!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "usuarios";
                            }
                        });
                    </script>';
                }
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Revisar parametros!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = "usuarios";
                        }
                    });
                </script>';
            }
        }
    }


    static public function ctrMostrarUsuarios($item, $valor){
        $tabla = "usuarios";
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        return $respuesta;
    }

    static public function ctrMostrarFichasSede($item, $valor){
        $tabla = "fichas";
        $respuesta = ModeloUsuarios::mdlMostrarFichasSede($tabla, $item, $valor);
        return $respuesta;
    }

     
   
    static public function ctrCambiarCondicionUsuario($idUsuario, $condicion) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        
        // El rol del admin es (rol 9)
        
        if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "9") {
            error_log("Acceso denegado - Rol actual: " . ($_SESSION["rol"] ?? 'no definido'));
            return "acceso_denegado";
        }

        $tabla = "usuarios";
        $datos = array(
            "id_usuario" => $idUsuario,
            "condicion" => $condicion,
            "id_usuario_editor" => $_SESSION["id_usuario"]
        );

        $respuesta = ModeloUsuarios::mdlCambiarCondicionUsuario($tabla, $datos);
        return $respuesta;
}
   
    static public function ctrEditarUsuario() {
    // Verifica que se hayan enviado los campos mínimos para editar usuario
    if (isset($_POST["idEditUsuario"]) && isset($_POST["editNombre"]) && isset($_POST["selectEditSede"])) {

        // Validación con expresiones regulares para cada campo recibido
        if (
            preg_match('/^[a-zA-ZñÑáéíóÁÉÍÓÚ ]+$/', $_POST["editNombre"]) &&
            preg_match('/^[a-zA-ZñÑáéíóÁÉÍÓÚ ]+$/', $_POST["editApellido"]) &&
            preg_match('/^[a-zA-Z0-9]+$/', $_POST["editNumeroDocumento"]) &&
            preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $_POST["editEmail"]) &&
            preg_match('/^[0-9]+$/', $_POST["editTelefono"]) &&
            preg_match('/^[a-zA-Z0-9#\- ]+$/', $_POST["editDireccion"])
        ) {
            // Inicia sesión para obtener el ID del usuario que está haciendo la edición
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $idEditor = $_SESSION["id_usuario"] ?? null;

            // Valida que haya sesión activa (usuario logueado)
            if (!$idEditor) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "No hay sesión iniciada",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(() => { window.location = "login"; });
                </script>';
                return;
            }

            // Si el usuario editado NO es aprendiz (rol 6), se limpian sede y ficha
            if ($_POST["EditRolUsuario"] != 6) {
                $sede = "";
                $ficha = "";
            } else {
                // Si es aprendiz, se asignan sede y ficha desde el formulario
                $sede = $_POST["selectEditSede"];
                $ficha = $_POST["selectEditIdFicha"];
            }

            // Obtiene los datos actuales del usuario desde la base de datos
            $usuario = self::ctrMostrarUsuarios("id_usuario", $_POST["idEditUsuario"]);

            // Variables para comparar el número de documento antes y después de la edición
            $numeroDocumentoAnterior = $usuario["numero_documento"];
            $numeroDocumentoNuevo = $_POST["editNumeroDocumento"];

            // Obtiene la ruta actual de la foto del usuario
            $rutaFoto = $usuario["foto"];

            // Si el número de documento cambió y la foto no es la predeterminada,
            // mueve la foto a la carpeta correspondiente al nuevo número de documento
            if (
                $numeroDocumentoAnterior != $numeroDocumentoNuevo &&
                $rutaFoto != "vistas/img/usuarios/default/anonymous.png" &&
                strpos($rutaFoto, "vistas/img/usuarios/{$numeroDocumentoAnterior}/") !== false
            ) {
                // Crea el nuevo directorio si no existe
                $nuevoDirectorio = "vistas/img/usuarios/{$numeroDocumentoNuevo}";
                if (!file_exists($nuevoDirectorio)) {
                    mkdir($nuevoDirectorio, 0755, true);
                }

                // Extrae solo el nombre del archivo actual
                $nombreArchivo = basename($rutaFoto);
                $nuevaRutaFoto = "{$nuevoDirectorio}/{$nombreArchivo}";

                // Copia la imagen a la nueva ruta
                if (file_exists($rutaFoto)) {
                    copy($rutaFoto, $nuevaRutaFoto);
                    $rutaFoto = $nuevaRutaFoto;
                }
            }

            // Nombre de la tabla donde se actualizan los datos
            $tabla = "usuarios";

            // Arreglo con los datos para enviar al modelo y actualizar usuario
            $datos = array(
                "id_usuario" => $_POST["idEditUsuario"],
                "tipo_documento" => $_POST["editTipoDocumento"],
                "numero_documento" => $_POST["editNumeroDocumento"],
                "nombre" => $_POST["editNombre"],
                "apellido" => $_POST["editApellido"],
                "correo_electronico" => $_POST["editEmail"],
                "telefono" => $_POST["editTelefono"],
                "direccion" => $_POST["editDireccion"],
                "genero" => $_POST["editGenero"],
                "id_rol" => $_POST["EditRolUsuario"],
                "foto" => $rutaFoto,
                "id_sede" => $sede,
                "id_ficha" => $ficha,
                "idRolOriginal" => $_POST["rolOriginal"],
                "idFichaOriginal" => $_POST["fichaOriginal"],
                "id_usuario_editor" => $idEditor // ID del usuario que hace la edición para auditoría
            );

            // Llama al modelo para actualizar datos en la base de datos
            $respuesta = ModeloUsuarios::mdlEditarUsuario($tabla, $datos);

            // Mensajes con SweetAlert según el resultado de la actualización
            if ($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡El usuario ha sido actualizado correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = "usuarios";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error al actualizar el usuario!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = "usuarios";
                        }
                    });
                </script>';
            }
        } else {
            // Si falla alguna validación de los campos
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "¡Revisar parámetros!",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = "usuarios";
                    }
                });
            </script>';
        }
    }
}

    static public function ctrCambiarEstadoUsuario($id, $estado) {
    // Iniciar sesión si aún no está activa
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $idEditor = $_SESSION["id_usuario"] ?? null;

    // Validar que haya sesión activa
    if (!$idEditor) {
        return false;
    }

    // Armar datos para enviar al modelo
    $tabla = "usuarios";
    $datos = [
        "id_usuario" => $id,
        "estado" => $estado,
        "id_usuario_editor" => $idEditor // Para auditoría
    ];

    // Enviar al modelo para guardar cambio de estado
    return ModeloUsuarios::mdlCambiarEstadoUsuario($tabla, $datos);
}
}
?>