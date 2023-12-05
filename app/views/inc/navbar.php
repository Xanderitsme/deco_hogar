<nav class="navbar">
    <div class="navbar-brand">
        <a class="navbar-item" href="<?php echo APP_URL; ?>dashboard/">
            <div><h1 class="title" style="font-weight: 700;">Deco Hogar</h1></div>
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
                    <a class="navbar-item" href="#" id="btn_exit" >
                        Nuevo producto
                    </a>
                    <a class="navbar-item" href="#" id="btn_exit" >
                        Nuevo producto
                    </a>
                </div>
            </div>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="<?php echo APP_URL; ?>clientes/">
                    Clientes
                </a>
                <div class="navbar-dropdown is-boxed">
                    <a class="navbar-item" href="#" id="btn_exit" >
                        Nuevo cliente
                    </a>
                </div>
            </div>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Proformas de venta
                </a>
                <div class="navbar-dropdown is-boxed">
                    <a class="navbar-item" href="#" id="btn_exit" >
                        Nueva proforma de venta
                    </a>
                </div>
            </div>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Inventario
                </a>
                <div class="navbar-dropdown is-boxed">
                    <a class="navbar-item" href="#" id="btn_exit" >
                        Historial de movimientos de inventario
                    </a>
                </div>
            </div>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Trabajadores
                </a>
                <div class="navbar-dropdown is-boxed">
                    <a class="navbar-item" href="<?php echo APP_URL; ?>nuevoTrabajador/" id="btn_exit" >
                        Registrar trabajador
                    </a>
                    <a class="navbar-item" href="#" id="btn_exit" >
                        Usuarios
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>nuevoUsuario/" id="btn_exit" >
                        Registrar usuario
                    </a>
                </div>
            </div>
        </div>
        
        <div class="navbar-end">
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    Juan Daniel Rojas Cevallos<br>
                    Vendedor
                </a>
                <div class="navbar-dropdown is-boxed">
                    <a class="navbar-item" href="<?php echo APP_URL; ?>logout/" id="btn_exit" >
                        Salir
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>