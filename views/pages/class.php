<?php
$title = "Home";
function get_content(){
$class_id = $_GET['id'];
$user_id = $_SESSION['user_info']['id'];
require_once "../../controllers/connection.php";

$query = "SELECT classes.name, classes.description, classes.create_date, classes.owner, users.name AS owner_name FROM classes 
INNER JOIN users ON classes.owner = users.id
WHERE classes.id = '$class_id'";
$result = mysqli_query($cn, $query);
$class_info = mysqli_fetch_array($result, MYSQLI_ASSOC);
// var_dump($class_info);

//check if user is in class
$query_check = "SELECT user_id FROM students WHERE user_id = '$user_id' AND class_id = '$class_id'";
if(mysqli_num_rows(mysqli_query($cn, $query_check)) == 0 && $class_info['owner'] != $user_id){ ?>
    <script>
        alert("Class not found!");
        window.location.replace("/");
    </script>
<?php
    die();
}
?>

<link rel="stylesheet" href="../../assets/styles/style.css">

<div class="container-lg py-5 px-lg-5">
<h1><?php echo $class_info['name']?></h1>
<p class="my-2"><?php echo $class_info['description']?></p>
<small class="m-0 text-body-tertiary">Teacher <?php echo $class_info['owner_name']?> <span class="mx-2">•</span> Created on <?php echo date("d M Y", $class_info['create_date'])?></small>


<div class="row g-5 px-xl-5 py-5">

<div class="col-12">
    <button class="form-control btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal">Post something...</button>
<!-- <a tabindex="0" class="btn btn-outline-danger form-control" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Dismissible popover" data-bs-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->

<div class="modal fade" tabindex="-1" id="modal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">What do you want to post?</h5>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-6"><button class="form-control btn btn-outline-info" data-bs-target="#addModal" data-bs-toggle="modal" id="materialbtn">Material</button></div>
            <div class="col-6"><button class="form-control btn btn-outline-warning" data-bs-target="#addModal" data-bs-toggle="modal" id="assignmentbtn">Assignment</button></div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="addModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
  <div class="modal-dialog modal-xl modal-fullscreen-sm-down modal-dialog-scrollable">
    <div class="modal-content">
    <form action="/controllers/posts/add.php" method="POST" enctype="multipart/form-data">
      <div class="modal-header">
        <!-- title and score -->
        <input class="modal-title form-control w-50 fs-5 me-3" name="title" placeholder="Material title">
        <input type="number" class="form-control w-auto fs-5" name="totalScore" id="score" placeholder="Total score" min="0">

        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <!-- body -->
        <textarea name="body" class="form-control mb-3" rows="7" placeholder="Content here..."></textarea>

        <!-- file -->
        <input type="file" class="form-control" name="file">
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" data-bs-target="#modal" data-bs-toggle="modal" type="button">Go back</button>
        <button class="btn btn-success">Save</button>
      </div>
    </form>
    </div>
  </div>
</div>

<?php
$query = "SELECT * FROM posts WHERE class_id = '$class_id'";
$result = mysqli_query($cn, $query);
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach($posts as $post):
?>
<div class="col-12">
    <div class="card">
    <div class="card-header">
        Featured
    </div>
    <div class="card-body">
        <h5 class="card-title">Special title treatment</h5>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="btn btn-primary">Go somewhere</a>
    </div>
    </div>
</div>
<?php endforeach?>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })
})

const materialBtn = document.getElementById('materialbtn');
const assignmentBtn = document.getElementById('assignmentbtn');
const score = document.getElementById('score');

materialBtn.addEventListener('click', () => {
    score.classList.add('d-none');
});

assignmentBtn.addEventListener('click', () => {
    score.classList.remove('d-none');
});
</script>
<?php
}
require_once '../template/layout.php';