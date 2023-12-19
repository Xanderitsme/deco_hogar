<div class="mt-6 pt-6">
	<div class="columns is-flex is-justify-content-center">
		<div class="contenedor-imagen">
			<figure class="imagen">
				<?php
				if (is_file("app/views/fotos/" . $_SESSION["foto"])) {
					echo '<img src="' . APP_URL . 'app/views/fotos/' . $_SESSION["foto"] . '" alt="foto de perfil del usuario">';
				} else {
					echo '<img src="' . APP_URL . 'app/views/fotos/default.png" alt="foto de perfil del usuario">';
				}
				?>
			</figure>
		</div>
	</div>
	<div class="columns is-flex is-justify-content-center mt-3">
		<h2 class="subtitle">¡Bienvenid@ <?php echo $_SESSION['nombres'] . " " . $_SESSION['apellidos']; ?>!</h2>
	</div>
</div>