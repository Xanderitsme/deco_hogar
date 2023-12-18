<div class="borde"></div>
<div class="container is-fluid">
	<?php

	use app\controllers\userController;
	use app\controllers\trabajadorController;

	$insUsuario = new userController();

	$id = $insLogin->limpiarCadena($url[1]);

	if ($id == $_SESSION['id']) { ?>
		<h1 class="title">Mi cuenta</h1>
		<h2 class="subtitle">Actualizar cuenta</h2>
	<?php } elseif ($_SESSION['cargoId'] == 1 && $id != 1) { ?>
		<h1 class="title">Usuarios</h1>
		<h2 class="subtitle">Actualizar usuario</h2>
	<?php } else {
		if (headers_sent()) {
      echo "
        <script>
          window.location.href = '" . APP_URL . "403';
        </script>
      ";
    } else {
      header("Location: " . APP_URL . "403");
    }
	}?>
</div>

<div class="container pb-4" style="max-width: 65%;">
	<?php
	include "./app/views/inc/btn_back.php";

	$datos = $insUsuario->obtenerDatosUsuario($id);

	if ($_SESSION['id'] != 1 && $datos['cargoID'] == 1 && $id != $_SESSION['id']) {
		include "./app/views/inc/error_alert.php";
		exit();
	}

	if (is_null($datos)) {
		include "./app/views/inc/error_alert.php";
		exit();
	}
	?>
	<h2 class="title has-text-centered">
		<?php
		echo $datos['nombres'] . " " . $datos['apellidos'];
		?>
	</h2>
	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="modulo_usuario" value="actualizar">
		<input type="hidden" name="usuario_id" value="<?php echo $id; ?>">

		<div class="columns">
			<div class="column">
				<div class="control">
					<label>
						Nombre de usuario
						<input class="input" type="text" name="usuario_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" value="<?php echo $datos['usuario']; ?>">
					</label>
				</div>
				<div class="control pt-3">
					<label>
						Cargo
						<select class="input" name="trabajador_cargo" pattern="[1-9]{1,3}" maxlength="3">
							<?php

							$insUsuario = new trabajadorController();

							if ($_SESSION['id'] == $id) {
								echo '<option value="' . $_SESSION['cargoId'] . '" selected>' . $_SESSION['cargo'] . '</option>';
							} elseif ($_SESSION['id'] == 1) {
								echo $insUsuario->obtenerCargosNoCargador($datos['cargoID']);
							} elseif ($_SESSION['cargoId'] == 1 && $datos['cargoID'] != 1) {
								echo $insUsuario->obtenerCargosNoCargadorAdmin($datos['cargoID']);
							} else {
								echo '<option value="' . $datos['cargoID'] . '" selected>' . $datos['nombre_cargo'] . '</option>';
							}
							?>
						</select>
					</label>
				</div>
			</div>
			<div class="column">
				<div class="file is-boxed is-justify-content-center">
					<label class="file-label">
						<div class="contenedor-imagen">
							<figure class="imagen">
								<?php
								if (is_file("app/views/fotos/" . $datos["foto"])) {
									echo '<img id="imagen-usuario" src="' . APP_URL . 'app/views/fotos/' . $datos["foto"] . '" alt="foto de perfil del usuario">';
								} else {
									echo '<img id="imagen-usuario" src="' . APP_URL . 'app/views/fotos/default.png" alt="foto de perfil del usuario">';
								}
								?>
							</figure>
						</div>
						<input class="file-input" type="file" name="usuario_foto" accept=".jpg, .png, .jpeg" onchange="mostrarImagenPreview(event)">
					</label>
				</div>
			</div>
		</div>
		<p class="pb-3">
			Si desea actualizar la clave de este usuario llene los dos campos, en caso contrario deje los campos en blanco
		</p>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>
						Nueva clave
						<input class="input" type="password" name="usuario_clave_1" pattern="[a-zA-Z0-9$@._\-]{5,20}" maxlength="20">
					</label>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>
						Repetir nueva clave
						<input class="input" type="password" name="usuario_clave_2" pattern="[a-zA-Z0-9$@._\-]{5,20}" maxlength="20">
					</label>
				</div>
			</div>
		</div>
		<br>
		<p class="pb-3">
			Para poder actualizar estos datos, es necesario que ingrese sus credenciales de acceso actuales
		</p>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>
						Nombre de usuario *
						<input class="input" type="text" name="administrador_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required>
					</label>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>
						Clave *
						<input class="input" type="password" name="administrador_clave" pattern="[a-zA-Z0-9$@._\-]{5,20}" maxlength="20" required>
					</label>
				</div>
			</div>
		</div>
		<span>* Obligatorio</span>
		<p class="has-text-centered">
			<button type="submit" class="button is-success is-rounded">Actualizar</button>
		</p>
	</form>