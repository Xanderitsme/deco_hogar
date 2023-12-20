use deco_hogar;

-- create table elementos_uuid (
--   id binary(16) default(uuid_to_bin(uuid())),
--   primary key (id)
-- );

-- select bin_to_uuid from elementos_uuid;

-- Actualizar Subtotal detalle
update detalles
  set detalles.subtotal = (
    select (detalles.cantidad * productos.precio_venta) from productos
    where detalles.`ID` = productos.`ID`
);


-- Actualizar Total factura
update proformas_venta
  set proformas_venta.total = (
    select sum(detalles.subtotal) from detalles
    where detalles.`proforma_ventaID` = proformas_venta.`ID`
    group by detalles.`proforma_ventaID`
);


-- realizar venta

insert into clientes (tipo, numero_contacto, nombres, apellidos, DNI) values
(1, "946454873", "Dany Dorian", "Isuiza", "48282732");

insert into proformas_venta (total, `clienteID`, `vendedorID`, estado) values
(0, 2, 7, 1);

insert into detalles (cantidad, subtotal, `productoID`, `proforma_ventaID`) values
(3, 209.97, 3, 6);

insert into ventas (metodo_pago, `proforma_ventaID`, `cajeroID` ) values
(1, 6, 4);

delete from ventas where `ID` = 9;

select 
  clientes.nombres as nombres_cliente, 
  clientes.apellidos as apellidos_cliente,
  trabajadores.nombres as nombres_vendedor,
  trabajadores.apellidos as apellidos_vendedor
from proformas_venta
inner join clientes on clientes.ID = proformas_venta.`clienteID`
inner join trabajadores on trabajadores.`ID` = proformas_venta.`vendedorID`
where proformas_venta.`ID` = 1;

select
  productos.nombre,
  detalles.cantidad,
  detalles.subtotal
from ventas
inner join proformas_venta on ventas.proforma_ventaID = proformas_venta.`ID`
inner join detalles on detalles.`proforma_ventaID` = proformas_venta.`ID`
inner join productos on productos.ID = detalles.`productoID`
where ventas.ID = 1;

select
  productos.nombre,
  detalles.cantidad,
  detalles.subtotal
from proformas_venta
inner join detalles on detalles.`proforma_ventaID` = proformas_venta.`ID`
inner join productos on productos.ID = detalles.`productoID`
where proformas_venta.ID = 2;

select * from detalles;

update proformas_venta set estado = 3 where ID = 2;

