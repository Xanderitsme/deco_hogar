<div class="borde"></div>
<div class="container is-fluid mb-6">
	<h1 class="title pt-6">Trabajadores</h1>
</div>
<div class="container pb-6 pt-6">
	<h2 class="subtitle">Registrar trabajador</h2>
	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/trabajadorAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="modulo_trabajador" value="registrar">
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>
						Nombres *
						<input class="input" type="text" name="trabajador_nombres" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required>
					</label>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>
						Apellidos *
						<input class="input" type="text" name="trabajador_apellidos" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required>
					</label>
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>
						DNI *
						<input class="input" type="text" name="trabajador_dni" pattern="[0-9]{8}" maxlength="8" required>
					</label>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>
						Número de celular
						<input class="input" type="tel" name="trabajador_telefono" pattern="[0-9]{9}" maxlength="9">
					</label>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>
						Correo electronico
						<input class="input" type="email" name="trabajador_email" placeholder="nombre@dominio.com" maxlength="70">
					</label>
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>
						Fecha de contratación *
						<input class="input" type="date" name="trabajador_fecha_contratacion" required>
					</label>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>
						Sueldo *
						<input class="input" type="text" name="trabajador_sueldo" pattern="[0-9]{3,5}" maxlength="5" required>
					</label>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>
						Cargo *
						<select class="input" name="trabajador_cargo" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required>
							<option value="Vendedor">Vendedor</option>
							<option value="Cargador">Cargador</option>
							<option value="Cajero">Cajero</option>
							<option value="Almacenero">Almacenero</option>
							<option value="Administrador">Administrador</option>
						</select>
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