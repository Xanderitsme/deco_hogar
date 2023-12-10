<div class="borde"></div>
<div class="container is-fluid mt-6">
	<h1 class="title">Trabajadores</h1>
	<h2 class="subtitle">Lista de trabajadores</h2>
</div>
<div class="container is-fluid pb-6 pt-6" style="padding-left: 6rem; padding-right: 6rem;">

	<div class="form-rest mb-6 mt-6"></div>

	<?php

	use app\controllers\usuarioController;

	$insUsuario = new usuarioController();

	echo $insUsuario->listarTrabajadoresControlador($url[1], 10, $url[0], "");
	?>
</div>