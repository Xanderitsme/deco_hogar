<nav class="navbar">
  <div class="navbar-brand">
    <a class="navbar-item" href="<?php echo APP_URL; ?>dashboard/">
      <h1 class="title" style="font-weight: 700;">Deco Hogar</h1>
    </a>
    <div class="navbar-burger" data-target="navbarExampleTransparentExample">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>

  <div id="navbarExampleTransparentExample" class="navbar-menu">
    <div class="navbar-start">
      <a class="navbar-item" href="<?php echo APP_URL; ?>dashboard/">
        Dashboard
      </a>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="<?php echo APP_URL; ?>productos/">
          Productos
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="#">
            Nuevo producto
          </a>
          <a class="navbar-item" href="#">
            Nuevo producto
          </a>
        </div>
      </div>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="<?php echo APP_URL; ?>clientes/">
          Clientes
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="#">
            Nuevo cliente
          </a>
        </div>
      </div>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="#">
          Proformas de venta
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="#">
            Nueva proforma de venta
          </a>
        </div>
      </div>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="#">
          Inventario
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="#">
            Historial de movimientos de inventario
          </a>
        </div>
      </div>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="<?php echo APP_URL; ?>listaTrabajadores/">
          Trabajadores
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="<?php echo APP_URL; ?>nuevoTrabajador/">
            Registrar trabajador
          </a>
          <a class="navbar-item" href="<?php echo APP_URL; ?>listaUsuarios/">
            Usuarios
          </a>
          <a class="navbar-item" href="<?php echo APP_URL; ?>nuevoUsuario/">
            Registrar usuario
          </a>
        </div>
      </div>
    </div>

    <div class="navbar-end">
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link">
          <?php
          echo $_SESSION['nombres'] . " " . $_SESSION['apellidos'] . "<br>" . $_SESSION['cargo'];
          ?>
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="<?php echo APP_URL . "actualizarUsuario/" . $_SESSION['id'] . "/"; ?>">
            Mi cuenta
          </a>
          <a class="navbar-item" href="<?php echo APP_URL; ?>logout/" id="btn_exit">
            Salir
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>