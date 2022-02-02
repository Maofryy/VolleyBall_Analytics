    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

<!-- Main Content -->
<div id="content">

    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>

        <!-- Topbar Search -->
        <!--<form
            class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search d-flex justify-content-center">
            <div class="input-group">
                <input type="text" class="form-control bg-light border-0 small" placeholder="Rechercher..."
                    aria-label="Search" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
        </form>-->
        <form id="autocomplete" class="autocomplete d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search d-flex justify-content-center">
            <div class="input-group">
                <input class="autocomplete-input form-control bg-light border-0 small" placeholder="Rechercher..."
                        aria-label="Search" aria-describedby="basic-addon2"/>
                <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            <ul class="autocomplete-result-list"></ul>
        </form>

        <!-- Topbar Navbar -->
        <!-- Rercher <ul class="navbar-nav ml-auto"> dans le dossier template pour ajouter les notifications
            et ajouter les li correspondant -->
        <ul class="navbar-nav ml-auto">

            <!--<div class="topbar-divider d-none d-sm-block"></div>-->

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo session()->get('firstname') . ' ' . session()->get('firstname');?> </span>
                    <img class="img-profile rounded-circle"
                        src="<?= substr(base_url(),0 ,-12) . 'template/img/undraw_profile.svg"'?>>
                </a>
                <!-- Dropdown - User Information -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="<?= base_url() . '/public/Users/me'?>">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Mon profil
                    </a>
                    <!--<a class="dropdown-item" href="#">
                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                        Settings
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                        Activity Log
                    </a>-->
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Déconnexion
                    </a>
                </div>
            </li>

        </ul>

    </nav>
    <!-- End of Topbar -->