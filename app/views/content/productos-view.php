<div class="seccion-productos">
	<div class="panel-izquierdo">
		<h2 class="subtitle">Productos disponibles</h2>
		<form action="" method="post">
			<input class="borde sombra buscador" type="text" placeholder="Buscar productos...">
		</form>
		<div class="tabla borde sombra">
			<nav class="nav-productos">
				<span>Nombre</span>
				<span>Estado</span>
				<span>Precio</span>
				<span>Agregar</span>
			</nav>
			<?php for ($i = 0; $i < 50; $i++) { ?>
			<div class="detalles-producto">
				<span>Refrigeradora Razer gamer RGB full fps</span>
				<span>No disponible</span>
				<span>S. 1799.99</span>
				<span class="boton-tabla"><button class="borde sombra button is-dark">+</button></span>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="panel-derecho">
		<h2 class="subtitle">Productos seleccionados</h2>
		<div class="tabla borde sombra">
			<nav class="nav-venta">
				<span>Subtotal: S/. 1599.98</span>
				<span class="boton-tabla"><button class="borde sombra button is-dark">Generar proforma</button></span>
			</nav>
		</div>
		<div class="tabla borde sombra">
			<nav class="nav-prod-sel">
				<span>Nombre</span>
				<span>Editar</span>
				<span>Eliminar</span>
			</nav>
			<?php for ($i = 0; $i < 50; $i++) { ?>
			<div class="prod-sel">
				<span>Refrigeradora Razer gamer RGB full fps</span>
				<span class="boton-tabla"><button class="borde sombra button is-dark">+</button></span>
				<span class="boton-tabla"><button class="borde sombra button is-dark">+</button></span>
			</div>
			<?php } ?>
		</div>
	</div>
</div>