<div class="borde"></div>
<div class="container is-fluid mb-6">
	<h1 class="title pt-6">Productos</h1>
</div>
<div class="container pb-6 pt-6">
	<h2 class="subtitle">Registrar nuevo producto</h2>
	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/productoAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="modulo_producto" value="registrar">
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>
						Nombre del producto *
						<input class="input" type="text" name="producto_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\- ]{1,100}" maxlength="100" required>
					</label>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>
						Stock *
						<input class="input" type="number" name="producto_stock" pattern="[0-9]{1,4}" max="9999" required>
					</label>
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>
						Precio de venta *
						<input class="input" type="text" name="producto_precio_venta" pattern="^\d{1,5}(?:\.\d{1,2})?$" maxlength="8" placeholder="1199.99" required>
					</label>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>
						Precio de compra *
						<input class="input" type="text" name="producto_precio_compra" pattern="^\d{1,5}(?:\.\d{1,2})?$" maxlength="8" placeholder="1199.99" required>
					</label>
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>
						Descripción del producto *
						<input class="input" type="text" name="producto_descripcion" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{5,250}" maxlength="250" required>
					</label>
				</div>
			</div>
		</div>
		<span>* Obligatorio</span>
		<p class="has-text-centered">
			<button type="reset" class="button is-link is-light is-rounded">Limpiar</button>
			<button type="submit" class="button is-info is-rounded">Guardar</button>
		</p>
	</form>
</div>