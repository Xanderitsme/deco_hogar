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
