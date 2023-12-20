use deco_hogar;

-- DROP TRIGGER before_insert_detalles;
CREATE TRIGGER `before_insert_detalles` 
BEFORE INSERT ON `detalles` FOR EACH ROW BEGIN
    update productos set stock = (stock - new.cantidad)
    where `ID` = new.productoID;

    update proformas_venta set total = (total + new.subtotal)
    where `ID` = new.proforma_ventaID;
END


-- DROP TRIGGER after_delete_detalles;
CREATE TRIGGER `after_delete_detalles` 
AFTER DELETE ON `detalles` FOR EACH ROW BEGIN
    update productos set stock = (stock + old.cantidad)
    where `ID` = old.productoID;

    update proformas_venta set total = (total - old.subtotal)
    where `ID` = old.proforma_ventaID;
END


-- DROP TRIGGER after_update_detalles;
CREATE TRIGGER `after_update_detalles` 
BEFORE UPDATE ON `detalles` FOR EACH ROW BEGIN
    update productos set stock = (stock + old.cantidad)
    where `ID` = old.productoID;

    update productos set stock = (stock - new.cantidad)
    where `ID` = new.productoID;

    update proformas_venta set total = (total - old.subtotal)
    where `ID` = old.proforma_ventaID;

    update proformas_venta set total = (total + new.subtotal)
    where `ID` = new.proforma_ventaID;
END


-- DROP TRIGGER after_delete_ventas;
CREATE TRIGGER `after_delete_ventas` 
AFTER DELETE ON `ventas` FOR EACH ROW BEGIN
    update proformas_venta set estado = 3
    where old.proforma_ventaID = proformas_venta.ID;

    update caja set saldo = (saldo - (
        select proformas_venta.total from proformas_venta
        where proformas_venta.`ID` = old.proforma_ventaID
    ))
    where caja.`cajeroID` = old.cajeroID;
END


-- DROP TRIGGER after_insert_ventas;
CREATE TRIGGER `after_insert_ventas` 
AFTER INSERT ON `ventas` FOR EACH ROW BEGIN
    update proformas_venta set estado = 2
    where new.proforma_ventaID = proformas_venta.ID;
    
    update caja set saldo = (saldo + (
        select proformas_venta.total from proformas_venta
        where proformas_venta.`ID` = new.proforma_ventaID
    ))
    where caja.`cajeroID` = new.cajeroID;
END


-- DROP TRIGGER after_update_proformas_venta;
CREATE TRIGGER `after_update_proformas_venta` 
AFTER UPDATE ON `proformas_venta` FOR EACH ROW BEGIN

    if (new.estado = 2) then
        update productos 
        set 
            stock = (stock + (
                select cantidad from detalles where detalles.`proforma_ventaID` = old.ID
            ))
        where productos.`ID` = (
            select detalles.`productoID` from detalles where detalles.`proforma_ventaID` = old.ID
        );
    end if;
END

CREATE TRIGGER set_fecha_entrega_esperada
BEFORE INSERT ON ordenes_compra
FOR EACH ROW
BEGIN
    SET NEW.fecha_entrega_esperada = DATE_ADD(CURDATE(), INTERVAL 5 DAY);
END;