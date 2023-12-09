drop database if exists deco_hogar;
create database if not exists deco_hogar;
use deco_hogar;

-- drop table if exists cargos;
create table if not exists cargos (
    ID int auto_increment,
    nombre_cargo varchar (60) not null unique,
    primary key (ID)
);

-- drop table if exists trabajadores;
create table if not exists trabajadores (
    ID int auto_increment,
    nombres varchar (60) not null,
    apellidos varchar (60) not null,
    dni varchar (8) not null unique,
    telefono varchar (9) unique,
    email varchar (60) unique,
    fecha_contratacion date not null default (now()),
    sueldo float not null,
    cargoID int not null,
    foreign key (cargoID) references cargos (ID) on delete cascade,
    primary key (ID)
);

-- drop table if exists clientes;
create table if not exists clientes (
    ID int auto_increment,
    tipo enum('persona natural', 'persona juridica') not null,
    numero_contacto varchar (9) unique,
    fecha_registro date not null default (now()),
    nombres varchar (60),
    apellidos varchar (60),
    DNI varchar (8) unique,
    denominacion_social varchar (100) unique,
    RUC varchar (11) unique,
    primary key (ID)
);

-- drop table if exists proveedores;
create table if not exists proveedores (
    ID int auto_increment,
    denominacion_social varchar (100) not null,
    RUC varchar (11) not null unique,
    tiempo_envio int not null,
    numero_contacto varchar (9) unique,
    email varchar (60) not null unique,
    ubicacion varchar (60) not null,
    primary key (ID)
);

-- drop table if exists productos;
create table if not exists productos (
    ID int auto_increment,
    nombre varchar (100) not null unique,
    descripcion varchar (250) not null,
    precio float not null,
    stock int default 0 not null,
    primary key (ID)
);

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
    foreign key (productoID) references productos (ID) on delete cascade,
    foreign key (proveedorID) references proveedores (ID) on delete cascade,
    primary key (ID)
);

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
    orden_compraID int not null,
    foreign key (movimiento_inventarioID) references movimientos_inventario (ID) on delete cascade,
    foreign key (orden_compraID) references ordenes_compra (ID) on delete cascade
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
    clave varchar (100) not null,
    foto varchar (200),
    foreign key (trabajadorID) references trabajadores (ID) on delete cascade,
    primary key (ID)
);