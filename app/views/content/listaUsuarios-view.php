<div class="borde"></div>
<div class="container is-fluid mt-6">
	<h1 class="title">Usuarios</h1>
	<h2 class="subtitle">Lista de usuarios</h2>
</div>
<div class="container pb-6 pt-6">

	<div class="form-rest mb-6 mt-6"></div>

	<?php

	use app\controllers\usuarioController;

	$insUsuario = new usuarioController();

	echo $insUsuario->listarUsuariosControlador($url[1], 10, $url[0], "");
	?>
</div>