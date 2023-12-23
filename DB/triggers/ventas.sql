use deco_hogar;

-- DROP TRIGGER after_insert_ventas;
DELIMITER $$
CREATE TRIGGER `after_insert_ventas` 
AFTER INSERT ON `ventas` FOR EACH ROW BEGIN
    update proformas_venta set estado = 2
    where proformas_venta.ID = new.proforma_ventaID;
    
    update caja set saldo = (saldo + (
        select proformas_venta.total from proformas_venta
        where proformas_venta.`ID` = new.proforma_ventaID
    ))
    where caja.`cajeroID` = new.cajeroID;
END$$
DELIMITER ;


-- DROP TRIGGER after_delete_ventas;
DELIMITER $$
CREATE TRIGGER `after_delete_ventas` 
AFTER DELETE ON `ventas` FOR EACH ROW BEGIN
    update proformas_venta set estado = 3
    where proformas_venta.ID = old.proforma_ventaID;

    update caja set saldo = (saldo - (
        select proformas_venta.total from proformas_venta
        where proformas_venta.`ID` = old.proforma_ventaID
    ))
    where caja.`cajeroID` = old.cajeroID;
END$$
DELIMITER ;
