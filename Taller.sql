CREATE DATABASE taller_mecanico;
USE taller_mecanico;

CREATE TABLE CLIENTE (
    id_cliente      INT           NOT NULL AUTO_INCREMENT,
    nombre          VARCHAR(80)   NOT NULL,
    apellido_pat    VARCHAR(50)   NOT NULL,
    apellido_mat    VARCHAR(50),
    telefono        VARCHAR(15)   NOT NULL,
    correo          VARCHAR(100),
    direccion       VARCHAR(200),
    fecha_registro  DATE          NOT NULL DEFAULT (CURRENT_DATE),
    activo          TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (id_cliente)
);

CREATE TABLE VEHICULO (
    id_vehiculo  INT          NOT NULL AUTO_INCREMENT,
    id_cliente   INT          NOT NULL,
    marca        VARCHAR(50)  NOT NULL,
    modelo       VARCHAR(50)  NOT NULL,
    anio         YEAR         NOT NULL,
    color        VARCHAR(30),
    placa        VARCHAR(10)  NOT NULL UNIQUE,
    km_actual    INT          DEFAULT 0,
    PRIMARY KEY (id_vehiculo),
    FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente)
);

CREATE TABLE ORDEN_SERVICIO (
    id_orden        INT           NOT NULL AUTO_INCREMENT,
    id_vehiculo     INT           NOT NULL,
    fecha_entrada   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega   DATETIME,
    descripcion     TEXT          NOT NULL,
    estatus         ENUM('RECIBIDO','EN_PROCESO','TERMINADO','ENTREGADO')
                    NOT NULL DEFAULT 'RECIBIDO',
    total           DECIMAL(10,2) DEFAULT 0.00,
    PRIMARY KEY (id_orden),
    FOREIGN KEY (id_vehiculo) REFERENCES VEHICULO(id_vehiculo)
);

CREATE TABLE INVENTARIO (
    id_refaccion    INT           NOT NULL AUTO_INCREMENT,
    nombre          VARCHAR(100)  NOT NULL,
    descripcion     VARCHAR(255),
    cantidad        INT           NOT NULL DEFAULT 0,
    precio_compra   DECIMAL(10,2) NOT NULL,
    precio_venta    DECIMAL(10,2) NOT NULL,
    stock_minimo    INT           NOT NULL DEFAULT 5,
    proveedor       VARCHAR(100),
    PRIMARY KEY (id_refaccion)
);

INSERT INTO CLIENTE (nombre, apellido_pat, apellido_mat, telefono, correo, direccion) VALUES
('Carlos',  'Pérez',    'López', '9991234567', 'cperez@email.com',    'Calle 60 #123, Mérida'),
('María',   'González', 'Ruiz',  '9997654321', 'mgonzalez@email.com', 'Calle 47 #89, Mérida'),
('Roberto', 'Díaz',     'Chan',  '9993456789', 'rdiaz@email.com',     'Calle 31 #45, Mérida');

INSERT INTO VEHICULO (id_cliente, marca, modelo, anio, color, placa, km_actual) VALUES
(1, 'Nissan',    'Sentra',  2019, 'Blanco', 'YUC-123-A', 45000),
(2, 'Toyota',    'Corolla', 2021, 'Gris',   'YUC-456-B', 28000),
(3, 'Chevrolet', 'Aveo',    2017, 'Rojo',   'YUC-789-C', 72000);

INSERT INTO ORDEN_SERVICIO (id_vehiculo, descripcion, estatus, total) VALUES
(1, 'Cambio de aceite y filtros',    'ENTREGADO',  850.00),
(2, 'Revisión de frenos y balatas',  'EN_PROCESO', 1200.00),
(3, 'Afinación mayor',               'RECIBIDO',   0.00);

INSERT INTO INVENTARIO (nombre, descripcion, cantidad, precio_compra, precio_venta, stock_minimo, proveedor) VALUES
('Aceite 5W-30 1L',    'Aceite de motor sintético',     20, 85.00,  140.00, 5, 'AutoPartes del Sur'),
('Filtro de aceite',   'Filtro compatible Nissan/Toyota', 15, 45.00, 90.00, 5, 'AutoPartes del Sur'),
('Balatas delanteras', 'Juego de balatas para sedán',    8,  220.00, 380.00, 3, 'Distribuidora Mérida');

SELECT * FROM CLIENTE;
SELECT * FROM VEHICULO;
SELECT * FROM ORDEN_SERVICIO;
SELECT * FROM INVENTARIO;

