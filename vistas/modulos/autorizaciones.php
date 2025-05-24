<div class="wrapper">
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Autorizaciones</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="table-responsive">
              <table id="tblAutorizaciones" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Usuario</th>
                    <th>Fecha Solicitud</th>
                    <th>Fecha Autorización</th>
                    <th>Fecha Entrega</th>
                    <th>Estado Préstamo</th>
                    <th>Firmas</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $item = null;
                $valor = null;
                
                $autorizaciones = ControladorAutorizaciones::ctrMostrarAutorizaciones($item, $valor);

                foreach ($autorizaciones as $key => $value) {
                  $itemUsuario = "id_usuario";
                  $valorUsuario = $value["id_usuario"];
                  
                  $usuario = ControladorUsuarios::ctrMostrarUsuarios($itemUsuario, $valorUsuario);
                  
                  $estadoPrestamo = isset($value["estado_prestamo"]) ? $value["estado_prestamo"] : "Pendiente";
                  
                  echo '<tr>
                          <td>'.$usuario["nombre"].' '.$usuario["apellido"].'</td>
                          <td>'.$value["fecha_solicitud"].'</td>
                          <td>'.$value["fecha_autorizacion"].'</td>
                          <td>'.$value["fecha_entrega"].'</td>
                          <td>'.$estadoPrestamo.'</td>
                          <td>
                              <div class="icheck-primary d-inline mx-1">
                              <input type="checkbox" id="firma1_'.$value["id_autorizacion"].'">
                              <label for="firma1_'.$value["id_autorizacion"].'"></label>
                            </div>
                            <div class="icheck-primary d-inline mx-1">
                              <input type="checkbox" id="firma2_'.$value["id_autorizacion"].'">
                              <label for="firma2_'.$value["id_autorizacion"].'"></label>
                            </div>
                            <div class="icheck-primary d-inline mx-1">
                              <input type="checkbox" id="firma3_'.$value["id_autorizacion"].'">
                              <label for="firma3_'.$value["id_autorizacion"].'"></label>
                            </div>
                          </td>
                          <td>
                            <button class="btn btn-info btn-sm btnVerDetalles" data-toggle="modal" data-target="#modalDetalles" 
                              data-id="'.$value["id_autorizacion"].'"
                              data-usuario="'.$usuario["nombre"].' '.$usuario["apellido"].'">
                              <i class="fas fa-eye"></i>
                            </button>
                          </td>
                        </tr>';
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Modal Detalles -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalDetallesLabel">Detalles de Autorización</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formAutorizacion" role="form" method="post">
              <input type="hidden" id="idAutorizacion" name="idAutorizacion">
              
              <div class="form-group">
                <label>Usuario:</label>
                <input type="text" class="form-control" id="nombreUsuario" readonly>
              </div>

              <div class="form-group">
                <label>Motivo de rechazo:</label>
                <textarea class="form-control" id="motivoRechazo" name="motivoRechazo" rows="3"></textarea>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-success btnAutorizar">Autorizar</button>
                <button type="submit" class="btn btn-danger btnRechazar">Rechazar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>