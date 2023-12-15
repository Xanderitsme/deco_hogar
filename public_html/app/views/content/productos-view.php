<?php

use app\controllers\productController;

$insProducto = new productController();
?>
<style>
	body,
	html {
		overflow: hidden;
	}
</style>
<div class="seccion-productos">
	<div class="panel-izquierdo">
		<h2 class="subtitle">Productos disponibles</h2>
		<form action="<?php echo APP_URL; ?>app/ajax/productoAjax.php" method="post">
			<input class="borde sombra buscador" type="text" placeholder="Buscar productos...">
			<input type="hidden" name="buscador_productos" value="buscar_producto">
		</form>
		<?php
		echo $insProducto->listarProductosVentaControlador("");
		?>
	</div>
	<div class="panel-derecho">
		<h2 class="subtitle">Productos a vender</h2>
		<?php
		echo $insProducto->listarProductosProformaControlador(1);
		?>
	</div>
</div>