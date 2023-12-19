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
      <?php if ($insLogin->permisoAccesoVista('proformas_venta')) { ?>
        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link">
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

      <?php if ($insLogin->permisoAccesoVista('clientes')) { ?>
        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link">
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

      <?php if ($insLogin->permisoAccesoVista('inventario')) { ?>
        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link">
            Inventario
          </a>
          <div class="navbar-dropdown is-boxed">
            <a class="navbar-item" href="<?php echo APP_URL; ?>listaMovimientosInventario/">
              Lista de movimientos de inventario
            </a>
            <?php if ($insLogin->permisoAccesoVista('productos')) { ?>
              <a class="navbar-item" href="<?php echo APP_URL; ?>nuevoProducto/">
                Nuevo producto
              </a>
              <a class="navbar-item" href="<?php echo APP_URL; ?>listaProductos/">
                Lista de productos
              </a>
            <?php } ?>
          </div>
        </div>
      <?php } ?>

      <?php if ($insLogin->permisoAccesoVista('ordenes_compra')) { ?>
        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link">
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

      <?php if ($insLogin->permisoAccesoVista('trabajadores')) { ?>
        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link">
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

      <?php if ($insLogin->permisoAccesoVista('usuarios')) { ?>
        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link">
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
        <a class="navbar-link" href="<?php echo APP_URL . "actualizarUsuario/" . $_SESSION['id'] . "/"; ?>" style="min-width: 150px;">
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