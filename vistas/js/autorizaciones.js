$(document).on('click', '.btnAutorizarSolicitud', function() {
    const idSolicitud = $(this).data('id');
    const rol = $(this).data('rol'); // Obtener rol del data attribute
    
    Swal.fire({
        title: `¿Estas Seguro De Autorizar Esta Solicitud?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Autorizar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/autorizaciones.ajax.php',
                method: 'POST',
                data: {
                    'id': idSolicitud,
                    'accion': 'autorizar',
                    'rol': rol // Usar variable JS
                },                success: function(respuesta) {
                    try {
                        respuesta = JSON.parse(respuesta);
                        Swal.fire(respuesta.titulo, respuesta.mensaje, respuesta.estado);
                    } catch (e) {
                        Swal.fire('Error', 'Respuesta inválida del servidor', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            });
        }
    });
});

$(document).on('click', '.btnRechazarSolicitud', function() {
    const idSolicitud = $(this).data('id');
    const rol = $(this).data('rol');
    
    Swal.fire({
        title: '¿Seguro deseas rechazar esta solicitud?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Rechazar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/autorizaciones.ajax.php',
                method: 'POST',
                data: {
                    'id': idSolicitud,
                    'accion': 'rechazar',
                    'rol': rol
                },
                success: function(respuesta) {
                    try {
                        respuesta = JSON.parse(respuesta);
                        Swal.fire(respuesta.titulo, respuesta.mensaje, respuesta.estado);
                    } catch (e) {
                        Swal.fire('Error', 'Respuesta inválida del servidor', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            });
        }
    });
});