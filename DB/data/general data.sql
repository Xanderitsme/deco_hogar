use deco_hogar;

insert into cargos (nombre_cargo) values 
("Administrador"),
("Almacenero"),
("Vendedor"),
("Cajero"),
("Cargador");

insert into trabajadores (nombres, apellidos, dni, telefono, email, sueldo, cargoID) values 
("Cuenta de ", "administrador", "59304390", '998559064', 'admin@dominio.com', 3500, 1 ),
('Juan José', 'López Candia', '45314037', '911741154', 'juanlopez@dominio.com', 2200, 2),
('Ana María', 'Pérez Estrada', '35418633', '982594329', 'anaperez@dominio.com', 2200, 3),
('María Laura', 'García Rodríguez', '61808103', '831435229', 'mariagarcia@dominio.com', 2500, 4),
('Pedro Alejandro', 'Martínez Ramírez', '43559749', '974170633', 'pedromartinez@dominio.com', 2000, 5),
('Carlos Jorge', 'Gómez Sánchez', '65230548', '906004775', 'carlosgomez@dominio.com', 2000, 5),
('David Roberto', 'Hernández Fernández', '78991149', '948573785', 'davidhernandez@dominio.com', 2000, 5);

insert into caja (saldo, cajeroID) values (0, 4);

insert into cuentas (trabajadorID, usuario, clave) values
(1, 'admin', '$2y$10$BB7LmBLe/2kzeGlOpZ7sg.lx6kNtWiGRQCFOkOtnJ3.HG84NDoIz6'),
(2, 'almacen', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(3, 'ventas2', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(4, 'caja1', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe');

update cuentas set foto = 'admin_85_170601.jpg' where cuentas.`ID` = 1;

insert into proveedores (denominacion_social, `RUC`, tiempo_envio, numero_contacto, email, ubicacion) values
("IMPORTACIONES RUBI S.A.", "20298463165", 15, "937467184", "importacionesrubi@dominio.com", "Cercado de Lima");


insert into clientes (tipo, numero_contacto, nombres, apellidos, `DNI`) values
(1, '981395227', 'Cliente', 'de prueba', '44966576');

insert into proformas_venta (total, `clienteID`, `vendedorID`, estado) values
(0, 1, 1, 1);

update detalles set detalles.subtotal = (
  (select productos.precio_venta from productos
  where productos.ID = detalles.`productoID`) * detalles.cantidad
);
