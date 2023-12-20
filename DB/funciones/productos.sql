use deco_hogar;

CREATE FUNCTION registrarProducto(
  pNombre varchar(100),
  pDescripcion varchar (250),
  pPrecio_venta decimal(7, 2),
  pPrecio_compra decimal(7, 2)
) RETURNS int
READS SQL DATA
BEGIN
  declare vProdId int;

  insert into productos (nombre, descripcion, precio_venta, precio_compra) values
  (pNombre, pDescripcion, pPrecio_venta, pPrecio_compra);
  
  set vProdId = LAST_INSERT_ID();

  return vProdId;
END;

CREATE FUNCTION solicitarProductos(
  pCantidad int,
  pProductoID int,
  pProveedorID int
) RETURNS int
READS SQL DATA
BEGIN
  declare vOrdenCompraID int;

  insert into ordenes_compra (cantidad, estado, `productoID`, `proveedorID`) values
  (pCantidad, 1, pProductoID, pProductoID);
  
  set vOrdenCompraID = LAST_INSERT_ID();

  return vOrdenCompraID;
END;
