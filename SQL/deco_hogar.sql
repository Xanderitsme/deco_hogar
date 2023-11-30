-- drop database if exists deco_hogar;
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

insert into personas (nombres, apellidos, `DNI`) VALUES
("Juan", "Perez", "47141326"),
("Maria", "Gomez", "57654321"),
("Ana", "Lopez", "23456789"),
("Laura", "Martinez", "48765432"),
("Pedro", "Rodriguez", "34567890"),
("Carlos", "Hernandez", "56789012"),
("Jorge", "Diaz", "65432109"),
("Angel", "Fernandez", "45678901");

-- drop table if exists cargos;
create table if not exists cargos (
    ID int auto_increment,
    nombre_cargo varchar (60) not null unique,
    primary key (ID)
);

insert into cargos (nombre_cargo) values 
("administrador"),
("vendedor"),
("cajero"),
("cargador");

-- drop table if exists trabajadores;
create table if not exists trabajadores (
    ID int auto_increment,
    sueldo float not null,
    personaID int not null unique,
    cargoID int,
    foreign key (personaID) references personas (ID) on delete cascade,
    foreign key (cargoID) references cargos (ID) on delete cascade,
    primary key (ID)
);

insert into trabajadores (sueldo, `personaID`, `cargoID`) values
(3000, 1, 1),
(1200, 2, 2),
(1200, 3, 2),
(1600, 4, 3),
(3000, 5, 1),
(2000, 6, 4),
(2000, 7, 4),
(2000, 8, 4);

-- drop table if exists areas_gestion;
create table if not exists areas_gestion (
    ID int auto_increment,
    nombre_area varchar (50) not null unique,
    primary key (ID)
);

insert into areas_gestion (nombre_area) values 
("general"),
("almacen");

-- drop table if exists administradores;
create table if not exists administradores (
    ID int auto_increment,
    trabajadorID int not null unique,
    area_gestionID int not null,
    foreign key (trabajadorID) references trabajadores (ID) on delete cascade,
    foreign key (area_gestionID) references areas_gestion (ID) on delete cascade,
    primary key (ID)
);

insert into administradores (`trabajadorID`, `area_gestionID`) values
(1, 1),
(5, 2);

-- drop table if exists clientes;
create table if not exists clientes (
    ID int auto_increment,
    primary key (ID)
);

insert into clientes () values ();

-- drop table if exists clientes_per_nat;
create table if not exists clientes_per_nat (
    clienteID int not null,
    nombres varchar (60) not null,
    apellidos varchar (60) not null,
    DNI varchar (8) not null unique,
    foreign key (clienteID) references clientes (ID) on delete cascade
);

SELECT * FROM clientes;

insert into clientes_per_nat (`clienteID`, nombres, apellidos, `DNI`) values
(1, "Noe", "Conchacalla", "sin DNI");

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
    ubicacion varchar (60) not null,
    primary key (ID)
);

-- https://importacionesrubi.com.pe/
insert into proveedores (denominacion_social, `RUC`, tiempo_envio, ubicacion) values
("IMPORTACIONES RUBI S.A.", "20298463165", 15, "Cercado de Lima");

-- drop table if exists productos;
create table if not exists productos (
    ID int auto_increment,
    nombre varchar (100) not null unique,
    descripcion varchar (250) not null,
    precio float not null,
    stock int default 0 not null,
    primary key (ID)
);

insert into productos (nombre, descripcion, precio, stock) values
("Refrigeradora", "Refrigeradora de acero inoxidable con dispensador de agua", 799.99, 20),
("Lavadora", "Lavadora de carga frontal con capacidad de 8 kg", 549.99, 15),
("Televisor LED", "Televisor LED de 55 pulgadas con resolución 4K", 899.99, 18),
("Microondas", "Horno de microondas de 1200W con funciones programables", 199.99, 25),
("Licuadora", "Licuadora de alto rendimiento con jarra de vidrio", 79.99, 30),
("Aire acondicionado", "Aire acondicionado split de 12000 BTU", 699.99, 15),
("Ventilador de torre", "Ventilador de torre oscilante con control remoto", 69.99, 30),
("Plancha de vapor", "Plancha de vapor con suela de cerámica", 49.99, 25),
("Hervidor eléctrico", "Hervidor eléctrico de agua con apagado automático", 39.99, 25),
("Batidora de vaso", "Batidora de vaso con cuchillas de acero inoxidable", 119.99, 18);

-- drop table if exists facturas;
create table if not exists facturas (
    ID int auto_increment,
    total float not null,
    fecha timestamp not null default CURRENT_TIMESTAMP,
    clienteID int not null,
    vendedorID int not null,
    cajeroID int not null,
    foreign key (clienteID) references clientes (ID) on delete cascade,
    foreign key (vendedorID) references trabajadores (ID) on delete cascade,
    foreign key (cajeroID) references trabajadores (ID) on delete cascade,
    primary key (ID)
);

-- drop table if exists detalles;
create table if not exists detalles (
    ID int auto_increment,
    cantidad int not null default 1,
    subtotal float not null,
    productoID int not null,
    facturaID int not null,
    foreign key (productoID) references productos (ID) on delete cascade,
    foreign key (facturaID) references facturas (ID) on delete cascade,
    primary key (ID)
);

-- drop table if exists pedidos;
create table if not exists pedidos (
    ID int auto_increment,
    cantidad int not null,
    productoID int not null,
    proveedorID int not null,
    administradorID int not null,
    foreign key (productoID) references productos (ID) on delete cascade,
    foreign key (proveedorID) references proveedores (ID) on delete cascade,
    foreign key (administradorID) references administradores (ID) on delete cascade,
    primary key (ID)
);

insert into facturas (total, `clienteID`, `vendedorID`, `cajeroID`) values
(0, );

