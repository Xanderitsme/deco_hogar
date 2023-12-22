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