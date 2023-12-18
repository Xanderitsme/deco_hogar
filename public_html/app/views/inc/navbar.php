<nav class="navbar">
  <div class="navbar-brand">
    <a class="navbar-item" href="<?php echo APP_URL; ?>dashboard/">
      <h1 class="title" style="font-weight: 700;">Deco Hogar</h1>
    </a>
    <div class="navbar-burger" data-target="navbar-links">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>

  <div id="navbar-links" class="navbar-menu">
    <div class="navbar-start">

      <?php if (in_array($_SESSION['cargoId'], [1, 3, 4])) { ?>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="<?php echo APP_URL; ?>nuevaProformaVenta/">
          Proformas de venta
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="<?php echo APP_URL; ?>nuevaProformaVenta/">
            Nueva proforma de venta
          </a>
          <a class="navbar-item" href="<?php echo APP_URL; ?>listaProformasVenta/">
            Lista de proformas de venta
          </a>
        </div>
      </div>
      <?php } ?>

      <?php if (in_array($_SESSION['cargoId'], [1, 3, 4])) { ?>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="<?php echo APP_URL; ?>listaClientes/">
          Clientes
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="<?php echo APP_URL; ?>nuevoCliente/">
            Nuevo cliente
          </a>
          <a class="navbar-item" href="<?php echo APP_URL; ?>listaClientes/">
            Lista de clientes
          </a>
        </div>
      </div>
      <?php } ?>

      <?php if (in_array($_SESSION['cargoId'], [1, 2])) { ?>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="<?php echo APP_URL; ?>listaMovimientosInventario/">
          Inventario
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="<?php echo APP_URL; ?>listaMovimientosInventario/">
            Lista de movimientos de inventario
          </a>
          <a class="navbar-item" href="<?php echo APP_URL; ?>nuevoProducto/">
            Nuevo producto
          </a>
          <a class="navbar-item" href="<?php echo APP_URL; ?>listaProductos/">
            Lista de productos
          </a>
        </div>
      </div>
      <?php } ?>

      <?php if (in_array($_SESSION['cargoId'], [1, 2])) { ?>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="<?php echo APP_URL; ?>listaOrdenesCompra/">
          Ordenes de compra
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="<?php echo APP_URL; ?>nuevaOrdenCompra/">
            Nueva orden de compra
          </a>
          <a class="navbar-item" href="<?php echo APP_URL; ?>listaOrdenesCompra/">
            Lista de ordenes de compra
          </a>
        </div>
      </div>
      <?php } ?>

      <?php if (in_array($_SESSION['cargoId'], [1])) { ?>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="<?php echo APP_URL; ?>listaTrabajadores/">
          Trabajadores
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="<?php echo APP_URL; ?>nuevoTrabajador/">
            Nuevo trabajador
          </a>
          <a class="navbar-item" href="<?php echo APP_URL; ?>listaTrabajadores/">
            Lista de trabajadores
          </a>
        </div>
      </div>
      <?php } ?>

      <?php if (in_array($_SESSION['cargoId'], [1])) { ?>
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="<?php echo APP_URL; ?>listaUsuarios/">
          Usuarios
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item" href="<?php echo APP_URL; ?>nuevoUsuario/">
            Nuevo usuario
          </a>
          <a class="navbar-item" href="<?php echo APP_URL; ?>listaUsuarios/">
            Lista de usuarios
          </a>
        </div>
      </div>
      <?php } ?>

    </div>

    <div class="navbar-end">
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" style="min-width: 150px;">
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