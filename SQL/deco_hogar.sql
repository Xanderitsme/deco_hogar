drop database if exists deco_hogar;
create database if not exists deco_hogar;
use deco_hogar;

-- drop table if exists personas;
create table if not exists personas (
    ID int auto_increment,
    nombres varchar (60) not null,
    apellidos varchar (60) not null,
    DNI varchar (8) not null unique,
    primary key (ID)
);

-- insert into personas (nombres, apellidos, `DNI`) VALUES
-- ("Juan", "Perez", "47141326"),
-- ("Maria", "Gomez", "57654321"),
-- ("Ana", "Lopez", "23456789"),
-- ("Laura", "Martinez", "48765432"),
-- ("Pedro", "Rodriguez", "34567890"),
-- ("Carlos", "Hernandez", "56789012"),
-- ("Jorge", "Diaz", "65432109"),
-- ("Angel", "Fernandez", "45678901");

-- drop table if exists cargos;
create table if not exists cargos (
    ID int auto_increment,
    nombre_cargo varchar (60) not null unique,
    primary key (ID)
);

-- insert into cargos (nombre_cargo) values 
-- ("administrador"),
-- ("vendedor"),
-- ("cajero"),
-- ("cargador");

-- drop table if exists trabajadores;
create table if not exists trabajadores (
    ID int auto_increment,
    sueldo float not null,
    personaID int not null unique,
    telefono varchar (9) not null unique,
    correo_electronico varchar (60) not null unique,
    fecha_contratacion date not null default (now()),
    cargoID int,
    foreign key (personaID) references personas (ID) on delete cascade,
    foreign key (cargoID) references cargos (ID) on delete cascade,
    primary key (ID)
);

-- insert into trabajadores (sueldo, `personaID`, `cargoID`) values
-- (3000, 1, 1),
-- (1200, 2, 2),
-- (1200, 3, 2),
-- (1600, 4, 3),
-- (3000, 5, 1),
-- (2000, 6, 4),
-- (2000, 7, 4),
-- (2000, 8, 4);

-- drop table if exists administradores;
create table if not exists administradores (
    ID int auto_increment,
    trabajadorID int not null unique,
    area_gestion enum('general', 'almacen') not null,
    foreign key (trabajadorID) references trabajadores (ID) on delete cascade,
    primary key (ID)
);

-- insert into administradores (`trabajadorID`, `area_gestion`) values
-- (1, 1),
-- (5, 2);

-- drop table if exists clientes;
create table if not exists clientes (
    ID int auto_increment,
    tipo enum('persona natural', 'persona juridica') not null,
    numero_contacto varchar (9) unique,
    fecha_registro date not null default (now()),
    primary key (ID)
);

-- insert into clientes (numero_contacto) values ('911473191');

-- drop table if exists clientes_per_nat;
create table if not exists clientes_per_nat (
    clienteID int not null,
    nombres varchar (60) not null,
    apellidos varchar (60) not null,
    DNI varchar (8) not null unique,
    foreign key (clienteID) references clientes (ID) on delete cascade
);

-- insert into clientes_per_nat (`clienteID`, nombres, apellidos, `DNI`) values
-- (1, "Noe", "Conchacalla", "sin DNI");

-- drop table if exists clientes_per_jur;
create table if not exists clientes_per_jur (
    clienteID int not null,
    denominacion_social varchar (100) not null,
    RUC varchar (11) not null unique,
    foreign key (clienteID) references clientes (ID) on delete cascade
);

-- drop table if exists proveedores;
create table if not exists proveedores (
    ID int auto_increment,
    denominacion_social varchar (100) not null,
    RUC varchar (11) not null unique,
    tiempo_envio int not null,
    numero_contacto varchar (9) unique,
    correo_electronico varchar (60) not null unique,
    ubicacion varchar (60) not null,
    primary key (ID)
);

-- https://importacionesrubi.com.pe/
-- insert into proveedores (denominacion_social, `RUC`, tiempo_envio, ubicacion) values
-- ("IMPORTACIONES RUBI S.A.", "20298463165", 15, "Cercado de Lima");

-- drop table if exists productos;
create table if not exists productos (
    ID int auto_increment,
    nombre varchar (100) not null unique,
    descripcion varchar (250) not null,
    precio float not null,
    stock int default 0 not null,
    primary key (ID)
);

-- insert into productos (nombre, descripcion, precio, stock) values
-- ("Refrigeradora", "Refrigeradora de acero inoxidable con dispensador de agua", 799.99, 20),
-- ("Lavadora", "Lavadora de carga frontal con capacidad de 8 kg", 549.99, 15),
-- ("Televisor LED", "Televisor LED de 55 pulgadas con resolución 4K", 899.99, 18),
-- ("Microondas", "Horno de microondas de 1200W con funciones programables", 199.99, 25),
-- ("Licuadora", "Licuadora de alto rendimiento con jarra de vidrio", 79.99, 30),
-- ("Aire acondicionado", "Aire acondicionado split de 12000 BTU", 699.99, 15),
-- ("Ventilador de torre", "Ventilador de torre oscilante con control remoto", 69.99, 30),
-- ("Plancha de vapor", "Plancha de vapor con suela de cerámica", 49.99, 25),
-- ("Hervidor eléctrico", "Hervidor eléctrico de agua con apagado automático", 39.99, 25),
-- ("Batidora de vaso", "Batidora de vaso con cuchillas de acero inoxidable", 119.99, 18);

-- drop table if exists proformas_venta;
create table if not exists proformas_venta (
    ID int auto_increment,
    total float not null,
    fecha timestamp not null default current_timestamp,
    clienteID int not null,
    vendedorID int not null,
    estado enum('pendiente', 'pagado') not null,
    foreign key (clienteID) references clientes (ID) on delete cascade,
    foreign key (vendedorID) references trabajadores (ID) on delete cascade,
    primary key (ID)
);

-- drop table if exists detalles;
create table if not exists detalles (
    ID int auto_increment,
    cantidad int not null default 1,
    subtotal float not null,
    unidad enum('Unidad') not null,
    productoID int not null,
    proforma_ventaID int not null,
    foreign key (productoID) references productos (ID) on delete cascade,
    foreign key (proforma_ventaID) references proformas_venta (ID) on delete cascade,
    primary key (ID)
);

-- drop table if exists ventas;
create table if not exists ventas (
    ID int auto_increment,
    metodo_pago enum('efectivo', 'tarjeta') not null,
    proforma_ventaID int not null,
    cajeroID int not null,
    foreign key (cajeroID) references trabajadores (ID) on delete cascade,
    foreign key (proforma_ventaID) references proformas_venta (ID) on delete cascade,
    primary key (ID)
);

-- drop table if exists devoluciones;
create table if not exists devoluciones (
    ID int auto_increment,
    motivo varchar (250) not null,
    fecha timestamp not null default current_timestamp,
    proforma_ventaID int not null,
    foreign key (proforma_ventaID) references proformas_venta (ID) on delete cascade,
    primary key (ID)
);

-- drop table if exists ordenes_compra;
create table if not exists ordenes_compra (
    ID int auto_increment,
    cantidad int not null,
    fecha_pedido date not null default (now()),
    fecha_entrega_esperada date not null,
    estado enum('pendiente', 'enviado', 'recibido') not null,
    productoID int not null,
    proveedorID int not null,
    administradorID int not null,
    foreign key (productoID) references productos (ID) on delete cascade,
    foreign key (proveedorID) references proveedores (ID) on delete cascade,
    foreign key (administradorID) references administradores (ID) on delete cascade,
    primary key (ID)
);

-- insert into proformas_venta (total, `clienteID`, `vendedorID`, `cajeroID`) values
-- (0, 1, 1, 1);

-- drop table if exists movimientos_inventario;
create table if not exists movimientos_inventario (
    ID int auto_increment,
    tipo_movimiento enum('entrada', 'salida') not null,
    cantidad int not null,
    fecha date not null default (now()),
    productoID int not null,
    foreign key (productoID) references productos (ID) on delete cascade,
    primary key (ID)
);

-- drop table if exists entradas_inventario;
create table if not exists entradas_inventario (
    movimiento_inventarioID int not null,
    foreign key (movimiento_inventarioID) references movimientos_inventario (ID) on delete cascade
);

-- drop table if exists salidas_inventario;
create table if not exists salidas_inventario (
    movimiento_inventarioID int not null,
    ventaID int not null,
    foreign key (ventaID) references ventas (ID) on delete cascade,
    foreign key (movimiento_inventarioID) references movimientos_inventario (ID) on delete cascade
);

-- drop table if exists cuentas;
create table if not exists cuentas (
    ID int not null auto_increment,
    trabajadorID int not null,
    usuario varchar (20) not null unique,
    foreign key (trabajadorID) references trabajadores (ID) on delete cascade,
    primary key (ID)
);

-- drop table if exists contrasenias;
create table if not exists contrasenias (
    cuentaID int not null,
    clave varchar (100) not null,
    foreign key (cuentaID) references cuentas (ID) on delete cascade
);