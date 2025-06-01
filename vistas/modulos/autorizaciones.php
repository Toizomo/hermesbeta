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
                <thead class="bg-dark">
                  <tr>
                    <th>Solicitante</th>
                    <th>Estado Préstamo</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Fecha Solicitud</th>
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
                          <td>'.$estadoPrestamo.'</td>
                          <td>'.date('Y-m-d H:i', strtotime($value["fecha_inicio"])).'</td>
                          <td>'.date('Y-m-d H:i', strtotime($value["fecha_fin"])).'</td>
                          <td>'.date('Y-m-d H:i', strtotime($value["fecha_solicitud"])).'</td>
                          <td>
                              <div class="icheck-primary d-inline mx-1">
                              <input type="checkbox" id="firma1'.$value["id_autorizacion"].'">
                              <label for="firma1'.$value["id_autorizacion"].'"></label>
                            </div>
                            <div class="icheck-primary d-inline mx-1">
                              <input type="checkbox" id="firma2'.$value["id_autorizacion"].'">
                              <label for="firma2'.$value["id_autorizacion"].'"></label>
                            </div>
                            <div class="icheck-primary d-inline mx-1">
                              <input type="checkbox" id="firma3'.$value["id_autorizacion"].'">
                              <label for="firma3'.$value["id_autorizacion"].'"></label>
                            </div>
                          </td>
                          <td>
                            <button class="btn btn-info btn-sm btnVerDetalles" id_usuario="'.$value["id_usuario"].'" data-toggle="modal" data-target="#modal-detalle">
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
  </div>
</div>
<!-- Modal Detalle Préstamo -->
<div class="modal fade" id="modal-detalle">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Detalle del Préstamo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <dl class="row">

                            <dt class="col-sm-4">Estado:</dt>
                            <dd class="col-sm-8" id="detalleTipoPrestamo"></dd>

                            <dt class="col-sm-4">Fecha Préstamo:</dt>
                            <dd class="col-sm-8" id="detalleFechaInicio"></dd>

                            <dt class="col-sm-4">Fecha Devolución:</dt>
                            <dd class="col-sm-8" id="detalleFechaFin"></dd>

                            <dt class="col-sm-4">Motivo:</dt>
                            <dd class="col-sm-8" id="detalleMotivoPrestamo"></dd>
                        </dl>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Equipos Solicitados</h5>
                            </div>
                            <div class="card-body p-10">
                                <table class="table table-bordered table-striped " id="tblDetallePrestamo">
                                    <thead>
                                        <tr>

                                            <th>ID</th>
                                            <th>Categoría</th>
                                            <th>Equipo</th>
                                            <th>etiqueta</th>
                                            <th>Serial</th>
                                            <th>Ubicación</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Aquí se cargarán los detalles del préstamo -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button class="btn btn-success btnAutorizarSolicitud" data-id="<?= $solicitud['id'] ?>"data-rol="<?= $_SESSION['rol'] ?>">Autorizar</button>
            <button class="btn btn-danger btnRechazarSolicitud" data-id="<?= $solicitud['id'] ?>"data-rol="<?= $_SESSION['rol'] ?>">Rechazar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
