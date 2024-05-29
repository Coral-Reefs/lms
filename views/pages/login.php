<?php
$title = "Login";
function get_content(){?>
<div class="container">
<div class="row">
<div class="col-md-6 mx-auto py-5">
<a href="/welcome.php">< Back to welcome page</a>

<form action="/controllers/users/process_login.php" method="POST">
    <div class="input-group my-3">
    <span class="input-group-text">@</span>
    <div class="form-floating">
        <input type="email" class="form-control" id="floatingInputGroup1" placeholder="Username" name="email">
        <label for="floatingInputGroup1">Email</label>
    </div>
    </div>

    <div class="form-floating mb-3">
        <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
        <label for="floatingPassword">Password</label>
    </div>

    <button class="btn btn-primary form-control p-2">Log in</button>
</form>
</div>
</div>
</div>

<?php
}
require_once '../template/layout.php';