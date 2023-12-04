<nav class="navbar">
    <div class="navbar-brand">
        <a class="navbar-item" href="<?php echo APP_URL; ?>dashboard/">
            <div><h1 class="title">Deco Hogar</h1></div>
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
            <a class="navbar-item" href="<?php echo APP_URL; ?>productos/">
                Productos
            </a>
            <a class="navbar-item" href="<?php echo APP_URL; ?>clientes/">
                Clientes
            </a>
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