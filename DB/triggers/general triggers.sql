use deco_hogar;

-- DROP TRIGGER after_update_proformas_venta;
CREATE TRIGGER `after_update_proformas_venta` 
AFTER UPDATE ON `proformas_venta` FOR EACH ROW BEGIN

    if (new.estado = 2) then
        update productos 
        set 
            stock = (stock + (
                select cantidad from detalles 
                where detalles.`proforma_ventaID` = old.ID
                and detalles.`productoID` = productos.ID
            ))
        where productos.`ID` = (
            select detalles.`productoID` from detalles 
            where detalles.`proforma_ventaID` = old.ID
            and detalles.`productoID` = productos.ID
        );
    end if;
END

-- CREATE TRIGGER set_fecha_entrega_esperada
-- BEFORE INSERT ON ordenes_compra
-- FOR EACH ROW
-- BEGIN
--     SET NEW.fecha_entrega_esperada = DATE_ADD(CURDATE(), INTERVAL 5 DAY);
-- END;