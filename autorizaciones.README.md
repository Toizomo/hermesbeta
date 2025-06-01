### 1. Creación de la tabla 'Autorizaciones',
-- Creación de la tabla 'autorizaciones'

'''sql
CREATE TABLE autorizaciones (
    id_autorizacion INT PRIMARY KEY,
    id_prestamo INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha_accion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    motivo_rechazo TEXT,
    
    -- Claves foráneas
    CONSTRAINT fk_autorizaciones_prestamos
        FOREIGN KEY (id_prestamo)
        REFERENCES prestamos(id_prestamo),
        
    CONSTRAINT fk_autorizaciones_usuario_rol
        FOREIGN KEY (id_usuario)
        REFERENCES usuario_rol(id_usuario)
);
'''

### 2. Insert de datos en la tabla 'autorizaciones'

'''sql

INSERT INTO autorizaciones (id_prestamo, id_usuario, motivo_rechazo) 
VALUES (2, 43, NULL);

INSERT INTO autorizaciones (id_prestamo, id_usuario, motivo_rechazo)
VALUES (4, 50, 'Equipos no disponibles para la fecha solicitada');

INSERT INTO autorizaciones (id_prestamo, id_usuario, motivo_rechazo)
VALUES (1, 55, NULL);

INSERT INTO autorizaciones (id_prestamo, id_usuario, motivo_rechazo)
VALUES (5, 53, 'Usuario con préstamos pendientes por devolver');

INSERT INTO autorizaciones (id_prestamo, id_usuario, motivo_rechazo)
VALUES (6, 46, NULL);
'''