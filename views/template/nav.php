<nav class="navbar bg-body-tertiary fixed-top p-3">
  <a class="container-fluid">

    <div class="d-flex align-items-center">
        <!-- menu icon -->
        <button class="navbar-toggler me-5" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="/">Eduportal</a>
    </div>

    <a href="/views/pages/profile.php" class="text-decoration-none text-dark"><?php echo $_SESSION['user_info']['isTeacher'] ? 'Teacher' : 'Student'?><img class=" ms-3 rounded-circle" width="45px" height="45px" src="<?php echo $_SESSION['user_info']['pfp'] ?>" alt=""></a>
    

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Eduportal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>

      <!-- menu content -->
      <div class="offcanvas-body d-flex flex-column justify-content-between">
        <!-- <div class="list-group list-group-flush">
            <a href="" class="list-group-item list-group-item-action rounded-4 border-0 py-3 d-flex align-items-center justify-content-between">
            </a>
        </div> -->
        <ul class="navbar-nav list-group list-group-flush flex-grow-1 pe-3">
          <div>
            <li class="nav-item list-group-item list-group-item-action rounded-pill border-0">
              <a class="nav-link" href="/"><i class="bi bi-house-fill me-3"></i> Home</a>
            </li>

            <?php
            $user_id = $_SESSION['user_info']['id'];
            $cn = mysqli_connect("localhost", "root", "", "lms");
            if($_SESSION['user_info']['isTeacher']){
                $query_classes = "SELECT id, name FROM classes
                    WHERE classes.owner_id = $user_id
                    ORDER BY create_date ASC";
            }else{
                $query_classes = "SELECT classes.id, classes.name FROM students
                JOIN classes ON students.class_id = classes.id
                WHERE students.user_id = $user_id;";
            }
            $result_classes = mysqli_query($cn, $query_classes);
            $classes = mysqli_fetch_all($result_classes, MYSQLI_ASSOC);
            foreach($classes as $class):
            ?>
            <li class="nav-item list-group-item list-group-item-action rounded-pill border-0">
              <a class="nav-link" href="/views/pages/class.php?id=<?php echo $class['id'] ?>"><?php echo $class['name']?></a>
            </li>
            <?php endforeach;?>
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