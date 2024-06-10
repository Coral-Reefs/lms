<?php
$title = "Profile";
function get_content(){
require_once "../../controllers/connection.php";
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_info']['id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($cn, $query);
$user = mysqli_fetch_assoc($result);
?>
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
            <img class="w-100" src="<?php echo $user['pfp'] ?>" alt="">
        </div>
        <div class="col-xl-5 col-md-7 d-flex align-items-centerj">
            <?php echo $user['name'] ?>
        </div>
    </div>
</div>
<?php }
require_once '../template/layout.php'; ?>