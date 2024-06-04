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
        <!-- title and marks -->
        <input type="hidden" name="class_id" value="<?php echo $class_id ?>">
        <input class="modal-title form-control w-50 fs-5 me-4" name="title" placeholder="Title" required>
        <input type="number" class="form-control w-auto" name="marks" id="marks" placeholder="Total marks" min="0">

        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <!-- body -->
        <textarea name="body" class="form-control mb-3" rows="7" placeholder="Content here..."></textarea>

        <!-- file -->
        <input type="file" class="form-control mb-3" name="files[]" multiple>

        <label for="duedate" id="duelabel">Due on:</label>
        <div class="d-flex" id="duedate">
          <input type="date" name="duedate" class="form-control w-auto">
          <input type="time" name="duetime" class="form-control w-auto">
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" data-bs-target="#modal" data-bs-toggle="modal" type="button">Go back</button>
        <input class="btn btn-success" type="submit" value="Post">
      </div>
    </form>
    </div>
  </div>
</div>

<div class="col-md-8">
<div class="row g-4">
<?php
$query = "SELECT * FROM posts WHERE class_id = '$class_id'";
$result = mysqli_query($cn, $query);
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach($posts as $post):
  $isAssignment = $post['marks'] != NULL;
?>
<div class="col-12">
  <a href="post.php?post_id=<?php echo $post['id']?>" class="text-decoration-none text-bg-light">
    <div class="card">
    <div class="card-header d-flex justify-content-between">
      
        <?php if($isAssignment): ?>
        <span class='badge text-bg-warning'>Assignment</span> 
        <small class="date"><?php echo date("Y-m-d H:i:s", $post['due']);?></small>
        <?php else:?>
        <span class='badge text-bg-primary'>Material</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <h5 class="card-title"><?php echo $post['title']?></h5>
        <p class="card-text"><?php echo nl2br(htmlspecialchars($post['body'])) ?></p>
    </div>
    <div class="card-footer">
      <small> <?php echo date('d M Y', $post['date']) ?></small>
    </div>
    </div>
  </a>
</div>
<?php endforeach?>
</div>  
</div>

<!-- right bar -->
<div class="col-md-4 d-none d-md-block">
  <div class="card mb-5">
    <div class="card-header">
      
    <h5 class="card-title m-0">Code</h5>
    </div>
    <div class="card-body d-flex justify-content-center align-items-center">
      <p class="card-text fs-1 d-inline m-1" id="class-id"><?php echo $class_id?></p>
      <button class="btn copy-btn" onclick="copyToClipboard()"><i class="bi bi-copy fs-5"></i></button>
      <button class="btn copy-btn p-0" onclick="copyToClipboard()"><i class="bi bi-link-45deg fs-3"></i></button>
    </div>
  </div>
  
  <div class="card">
    <div class="card-header">
      Upcoming
    </div>
    <div class="card-body">
      <?php 
      $query = "SELECT * FROM posts WHERE class_id = '$class_id' AND marks IS NOT NULL AND due IS NOT NULL 
      ORDER BY CAST(due AS UNSIGNED) ASC;";
      $result = mysqli_query($cn, $query);
      $sorted_assignments = mysqli_fetch_all($result, MYSQLI_ASSOC);
      if(!count($sorted_assignments)){
        echo "<span class='text-body-tertiary'>Woohoo! No work due soon</span>";
      }
      foreach($sorted_assignments as $upcoming):
      ?>
        <!-- upcoming assignments -->
        <div class="mb-3">
          <small class="m-0 d-block text-body-tertiary date"><?php echo date("d M Y H:i:s", $upcoming['due'])?></small>
          <a href="post.php?post_id=<?php echo $post['id']?>" class="link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-50-hover">
            <?php echo $upcoming['title']?>
          </a>
        </div>
        
      <?php endforeach?>
    </div>
  </div>


</div>

</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })
})

const materialBtn = document.getElementById('materialbtn');
const assignmentBtn = document.getElementById('assignmentbtn');
const marks = document.getElementById('marks');
const due = document.getElementById('duedate');
const duelabel = document.getElementById('duelabel');

materialBtn.addEventListener('click', () => {
    marks.classList.add('d-none');
    marks.required = false;
    due.classList.add('d-none');
    duelabel.classList.add('d-none');
});

assignmentBtn.addEventListener('click', () => {
    marks.classList.remove('d-none');
    marks.required = true;
    due.classList.remove('d-none');
    duelabel.classList.remove('d-none');
});

let timestamps = document.querySelectorAll('.date');
timestamps.forEach(timestamp => {
    let timeAgo = moment.utc(timestamp.innerText).local().fromNow();
    timestamp.innerText = timeAgo;
});

function copyToClipboard() {
    var textToCopy = document.getElementById('class-id').innerText;
    var tempTextArea = document.createElement('textarea');
    tempTextArea.value = textToCopy;
    document.body.appendChild(tempTextArea);
    tempTextArea.select();
    document.execCommand('copy');

    document.body.removeChild(tempTextArea);

    alert('Text copied to clipboard!');
}
</script>
<?php
}
require_once '../template/layout.php';