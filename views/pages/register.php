<?php
$title = "Register";
function get_content(){?>

<div class="container">
<div class="row">
<div class="col-md-6 mx-auto py-5">
<a href="/welcome.php">< Back to welcome page</a>

<form action="/controllers/users/process_register.php" method="POST">
    <div class="form-floating my-3">
        <input type="email" class="form-control" id="floatingEmail" placeholder="Email" name="email">
        <label for="floatingUsername">Email</label>
    </div>

    <div class="input-group mb-3">
    <span class="input-group-text">@</span>
    <div class="form-floating">
        <input type="text" class="form-control" id="floatingUsername" placeholder="Username" name="username">
        <label for="floatingUsername">Name</label>
    </div>
    </div>

    <label for="input-group">I am a...</label>
    

    <div class="card mb-3 form-control w-100" style="width: 18rem;">
    <ul class="list-group list-group-flush">
        <li class="list-group-item py-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" value="1" id="check1" name="role">
            <label class="form-check-label" for="check1">
                Teacher
            </label>
        </div>
        </li>

        <li class="list-group-item py-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" value="0" id="check2" name="role">
            <label class="form-check-label" for="check2">
                Student
            </label>
        </div>
        </li>
    </ul>
    </div>

    <div class="form-floating mb-3">
        <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
        <label for="floatingPassword">Password</label>
    </div>

    <div class="form-floating mb-3">
        <input type="password" class="form-control" id="floatingPassword" placeholder="ConfirmPassword" name="password2">
        <label for="floatingPassword">Confirm Password</label>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="check1" required>
        <label class="form-check-label" for="check1">I have read and agreed to terms and conditions</label>
    </div>

    <button class="btn btn-primary form-control p-2">Register</button>
</form>
</div>
</div>
</div>

<?php
}
require_once '../template/layout.php';