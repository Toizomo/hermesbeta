# Scripts SQL para la Base de Datos hermes002

Este archivo contiene instrucciones y consultas SQL que deben ejecutarse en la base de datos `hermes002` para realizar actualizaciones o modificaciones específicas.

**¡IMPORTANTE!**

* Asegúrate de estar conectado a la base de datos `hermes002` antes de ejecutar estas consultas.
* Realiza una copia de seguridad de la base de datos antes de ejecutar cualquier script, por si acaso necesitas revertir los cambios.
* Ejecuta las consultas en el orden en que aparecen en este archivo, si el orden es relevante.

## Consultas y Procedimientos

### 1. Agregar columna `foto` a la tabla `usuarios`

- Se debe agregar una columna llamada `foto` de tipo `VARCHAR(100)` a la tabla `usuarios`, ubicada después de la columna `genero`.

```sql
ALTER TABLE usuarios
ADD COLUMN foto VARCHAR(100) AFTER genero;
```

### 2. Ruta por defecto para la foto de usuario

- Al crear un nuevo usuario, el valor por defecto de la columna `foto` debe ser:  
    `vistas/img/usuarios/default/anonymous.png`

### 3. Creación automática de carpetas para fotos de usuario

- Cuando se crea un usuario nuevo:
    - Se debe crear automáticamente la carpeta `img` dentro de la carpeta `vistas` si no existe.
    - Dentro de `img`, se debe crear la carpeta `usuarios`.
    - Dentro de `usuarios`, se debe crear una carpeta con el número de documento del usuario.
    - En esa carpeta es donde se almacenará la foto del usuario.

### 4. Ejemplo de actualización de datos

```sql
-- Ejemplo: Actualizar la ruta de la foto para un usuario existente
UPDATE usuarios
SET foto = 'vistas/img/usuarios/default/anonymous.png'
WHERE id_usuario = 1;
```

### 5. Añadir tabla de 'autorizaciones'
- Se debe crear una tabla llamada `autorizaciones` con las siguientes columnas:

```sql
-- Creación de la tabla 'autorizaciones'
CREATE TABLE autorizaciones (
  id_autorizacion INT PRIMARY KEY AUTO_INCREMENT,
  id_prestamo INT NOT NULL,
  id_rol INT NOT NULL,
  id_usuario INT,
  fecha_accion TIMESTAMP,
  motivo_rechazo TEXT,

  UNIQUE (id_prestamo, id_rol),
  INDEX idx_prestamo (id_prestamo),
  INDEX idx_rol (id_rol),

  FOREIGN KEY (id_prestamo) REFERENCES prestamos(id_prestamo),
  FOREIGN KEY (id_rol) REFERENCES roles(id_rol),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
```

### 6. Insert de datos en la tabla 'autorizaciones'
- Se debe insertar un registro por cada préstamo existente en la tabla `prestamos`, con los siguientes valores:

```sql
-- Insertar registros en la tabla 'autorizaciones'
INSERT INTO autorizaciones (id_prestamo, id_rol, id_usuario, fecha_accion, motivo_rechazo) VALUES
(1, 3, 50, '2025-05-24 23:25:08', 'No hay disponibilidad de equipos en almacén'),   -- Almacén
(2, 5, 46, '2025-05-24 23:25:08', NULL),                                             -- Coordinación
(4, 7, 1,  '2025-05-24 23:25:08', NULL),                                             -- Instructor
(5, 2, 52, '2025-05-24 23:25:08', 'El préstamo excede el tiempo permitido'),        -- Mesa de ayuda
(6, 1, 53, '2025-05-24 23:25:08', NULL);                                             -- Líder TIC
```