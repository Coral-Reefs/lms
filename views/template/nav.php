<nav class="navbar bg-body-tertiary fixed-top p-3">
  <a class="container-fluid">

    <div class="d-flex align-items-center">
        <!-- menu icon -->
        <button class="navbar-toggler me-5" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="/">Eduportal</a>
    </div>

    <a href="/views/pages/profile.php"><img class="rounded-circle" width="45px" src="<?php echo $_SESSION['user_info']['pfp'] ?>" alt=""></a>
    

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Navbar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>

      <!-- menu content -->
      <div class="offcanvas-body d-flex flex-column justify-content-between">
        <ul class="navbar-nav justify-content-start flex-grow-1 pe-3">
        <div>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="/">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
        </div>

          <!-- <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Dropdown
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Action</a></li>
              <li><a class="dropdown-item" href="#">Another action</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
          </li> -->
        </ul>

        <a class="btn btn-primary" href="/controllers/users/process_logout.php">Logout</a>
      </div>
    </div>
  </div>
</nav>