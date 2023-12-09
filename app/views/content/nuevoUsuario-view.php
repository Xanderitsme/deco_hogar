<div class="borde"></div>
<div class="container is-fluid mt-6">
	<h1 class="title">Usuarios</h1>
</div>
<div class="container pb-6 pt-6">
	<h2 class="subtitle">Registrar usuario</h2>
	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data" >
		<input type="hidden" name="modulo_usuario" value="registrar">
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>
						ID trabajador *
						<input class="input" type="text" name="usuario_trabajador_id" pattern="[1-9]{1,4}" maxlength="4" required >
					</label>
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>
						Nombre de usuario *
						<input class="input" type="text" name="usuario_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required >
					</label>
				</div>
		  	</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>
						clave *
						<input class="input" type="password" name="usuario_clave_1" pattern="[a-zA-Z0-9$@._\-]{5,20}" maxlength="20" required >
					</label>
				</div>
			</div>
			<div class="column">
		    	<div class="control">
					<label>
						Repetir clave *
						<input class="input" type="password" name="usuario_clave_2" pattern="[a-zA-Z0-9$@._\-]{5,20}" maxlength="20" required >
					</label>
				</div>
		  	</div>
		</div>
		<div class="columns">
		  	<div class="column">
				<div class="file has-name is-boxed">
					<label class="file-label">
						<input class="file-input" type="file" name="usuario_foto" accept=".jpg, .png, .jpeg" >
						<span class="file-cta">
							<span class="file-label">
								Seleccione una foto
							</span>
						</span>
						<span class="file-name">JPG, JPEG, PNG. (MAX 5MB)</span>
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