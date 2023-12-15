insert into cargos (nombre_cargo) values 
("Administrador"),
("Almacenero"),
("Vendedor"),
("Cajero"),
("Cargador");

insert into trabajadores (nombres, apellidos, dni, telefono, email, sueldo, cargoID) values
("Cuenta de ", "administrador", "12345678",'123456789', 'admin@dominio.com', 0, 1),
('Juan', 'López', '12345578', '987654321', 'juan.lopez@example.com', 2500, 1),
('María', 'García', '23456789', '876543210', 'maria.garcia@example.com', 2300, 3),
('Pedro', 'Martínez', '34567890', '765432109', 'pedro.martinez@example.com', 2200, 2),
('Ana', 'Pérez', '45678901', '654321098', 'ana.perez@example.com', 2400, 4),
('Carlos', 'Gómez', '56789012', '543210987', 'carlos.gomez@example.com', 2600, 5),
('Laura', 'Rodríguez', '67890123', '432109876', 'laura.rodriguez@example.com', 2700, 1),
('David', 'Hernández', '78901234', '321098765', 'david.hernandez@example.com', 2550, 3),
('Sofía', 'Díaz', '89012345', '210987654', 'sofia.diaz@example.com', 2450, 2),
('Jorge', 'Sánchez', '90123456', '109876543', 'jorge.sanchez@example.com', 2650, 4),
('Marta', 'López', '01234567', '987654329', 'marta.lopez@example.com', 2600, 5),
('Alejandro', 'Ramírez', '11223344', '876543219', 'alejandro.ramirez@example.com', 2300, 1),
('Elena', 'Gutiérrez', '22446688', '765432101', 'elena.gutierrez@example.com', 2350, 3),
('Roberto', 'Fernández', '33445566', '654321095', 'roberto.fernandez@example.com', 2550, 2),
('Isabel', 'Torres', '44557799', '543210982', 'isabel.torres@example.com', 2700, 4),
('Pablo', 'Ruiz', '55668800', '432109871', 'pablo.ruiz@example.com', 2500, 5);

insert into cuentas (trabajadorID, usuario, clave) values
(1, 'admin', '$2y$10$BB7LmBLe/2kzeGlOpZ7sg.lx6kNtWiGRQCFOkOtnJ3.HG84NDoIz6'),
(2, 'administrador1', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(3, 'vendedor1', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(4, 'almacenero1', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(5, 'cajero1', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(6, 'cargador1', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(7, 'administrador2', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(8, 'vendedor2', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(9, 'almacenero2', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(10, 'cajero2', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(11, 'cargador2', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(12, 'administrador3', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(13, 'vendedor3', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(14, 'almacenero3', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(15, 'cajero3', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe'),
(16, 'cargador3', '$2y$10$TORfmQfZLMkBFHbYAkq4ief4Un1C91G45LBMxAiRt6V2l4s3tZQWe');

update cuentas set foto = 'admin_68.jpg' where cuentas.`ID` = 1;

-- https://importacionesrubi.com.pe/
-- insert into proveedores (denominacion_social, `RUC`, tiempo_envio, ubicacion) values
-- ("IMPORTACIONES RUBI S.A.", "20298463165", 15, "Cercado de Lima");

INSERT INTO productos (nombre, descripcion, precio_venta, precio_compra, stock) VALUES
('Refrigerador', 'Refrigerador de acero inoxidable con capacidad de 20 pies cúbicos', 899.99, 600.00, 50),
('Televisor LED', 'Televisor LED de 55 pulgadas con resolución 4K', 799.00, 500.00, 70),
('Licuadora de alta potencia', 'Licuadora con 1000W de potencia y jarra de vidrio resistente', 69.99, 45.00, 100),
('Lavadora automática', 'Lavadora automática de carga frontal con capacidad de 8 kg', 549.00, 400.00, 30),
('Aspiradora sin bolsa', 'Aspiradora ciclónica sin bolsa con accesorios para esquinas', 129.99, 90.00, 80),
('Horno eléctrico', 'Horno eléctrico de convección con capacidad de 30 litros', 149.50, 100.00, 60),
('Batidora de pie', 'Batidora de pie con tazón de acero inoxidable y 6 velocidades', 79.95, 55.00, 90),
('Cafetera programable', 'Cafetera automática programable con jarra térmica de acero', 89.00, 60.00, 40),
('Robot de cocina multifunción', 'Robot de cocina con 12 programas automáticos y pantalla táctil', 299.00, 220.00, 25),
('Plancha de vapor', 'Plancha de vapor con suela antiadherente y sistema antigoteo', 34.50, 20.00, 120),
('Ventilador de torre', 'Ventilador de torre oscilante con control remoto', 64.99, 45.00, 55),
('Máquina de hacer pan', 'Máquina automática para hacer pan con 15 programas', 119.00, 80.00, 35),
('Exprimidor de cítricos', 'Exprimidor eléctrico de cítricos con sistema antigoteo', 29.95, 18.00, 75),
('Plancha de pelo cerámica', 'Plancha de pelo con placas de cerámica y control de temperatura', 49.50, 35.00, 65),
('Máquina de coser', 'Máquina de coser portátil con 12 tipos de puntadas', 179.00, 120.00, 20),
('Freidora de aire', 'Freidora de aire con capacidad de 5.8 litros y pantalla digital', 89.99, 60.00, 85),
('Microondas con grill', 'Microondas con función grill y capacidad de 25 litros', 129.00, 90.00, 50),
('Tostadora de pan', 'Tostadora de pan con 7 niveles de tostado y bandeja extraíble', 24.95, 15.00, 110),
('Manta eléctrica', 'Manta eléctrica con control de temperatura y apagado automático', 39.99, 25.00, 70),
('Plancha de asar eléctrica', 'Plancha de asar eléctrica con superficie antiadherente', 49.00, 30.00, 45),
('Secadora de pelo iónica', 'Secadora de pelo iónica con difusor y concentrador', 54.50, 35.00, 80),
('Balanza digital de cocina', 'Balanza digital de precisión para cocina con pantalla LCD', 19.99, 12.00, 95),
('Afeitadora eléctrica', 'Afeitadora eléctrica recargable para hombres', 79.00, 50.00, 60),
('Cortina opaca', 'Cortina opaca de 2 paneles para bloquear la luz y conservar el calor', 29.50, 18.00, 100),
('Alfombra anti-fatiga', 'Alfombra ergonómica anti-fatiga para cocina o trabajo', 45.99, 30.00, 40),
('Cepillo eléctrico para dientes', 'Cepillo eléctrico recargable con temporizador y 3 modos de limpieza', 39.95, 25.00, 75),
('Báscula digital de baño', 'Báscula digital de vidrio templado con pantalla LED', 29.00, 20.00, 65),
('Dispensador de agua fría/caliente', 'Dispensador de agua con función fría y caliente', 99.00, 70.00, 25),
('Cortadora de césped eléctrica', 'Cortadora de césped eléctrica con 5 alturas de corte', 169.99, 120.00, 15);

insert into clientes (tipo, numero_contacto, nombres, apellidos, `DNI`) values
(1, '981395227', 'Cliente', 'de prueba', '44966576');

insert into proformas_venta (total, `clienteID`, `vendedorID`, estado) values
(0, 1, 1, 1);

insert into detalles (cantidad, subtotal, unidad, `productoID`, `proforma_ventaID`) values
(10, 0, 1, 1, 1),
(10, 0, 1, 2, 1),
(10, 0, 1, 3, 1),
(10, 0, 1, 4, 1);

update detalles set detalles.subtotal = (
  (select productos.precio_venta from productos
  where productos.ID = detalles.`productoID`) * detalles.cantidad
);
