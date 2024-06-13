<?php
$title = "Class";
function get_content(){
$class_id = $_GET['id'];
$user_id = $_SESSION['user_info']['id'];
require_once "../../controllers/connection.php";

$query = "SELECT classes.name, classes.description, classes.create_date, classes.owner_id, users.name AS owner_name FROM classes 
INNER JOIN users ON classes.owner_id = users.id
WHERE classes.id = '$class_id'";
$result = mysqli_query($cn, $query);
$class_info = mysqli_fetch_array($result, MYSQLI_ASSOC);

//check if user is in class
$query_check = "SELECT user_id FROM students WHERE user_id = '$user_id' AND class_id = '$class_id'";
if(mysqli_num_rows(mysqli_query($cn, $query_check)) == 0 && $class_info['owner_id'] != $user_id){ ?>
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
<div class="d-flex justify-content-between">   
  <div>
    <h1><?php echo $class_info['name']?></h1>
    <p class="my-2"><?php echo $class_info['description']?></p>
    <small class="m-0 text-body-tertiary">Teacher <?php echo $class_info['owner_name']?> <span class="mx-2">•</span> Created on <?php echo date("d M Y", $class_info['create_date'])?></small>
  </div>
  <div class="pe-5">
    <a class="btn rounded-circle fs-4 focus-ring" href="students.php?id=<?php echo $class_id?>"><i class="bi bi-people-fill"></i></a>
    <?php if($_SESSION['user_info']['isTeacher']): ?>
    <a class="btn rounded-circle fs-4 focus-ring" href="grades.php?id=<?php echo $class_id?>"><i class="bi bi-file-earmark-bar-graph-fill"></i></a>
    <a class="btn rounded-circle fs-4 focus-ring" href="attendance.php?id=<?php echo $class_id?>"><i class="bi bi-ui-checks"></i></a>
    <?php endif;?>
  </div>
</div>
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
$query = "SELECT * FROM posts WHERE class_id = '$class_id' ORDER BY id DESC";
$result = mysqli_query($cn, $query);
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach($posts as $post):
  $isAssignment = $post['marks'] != NULL;
  $post_id = $post['id'];

  if($isAssignment && !$_SESSION['user_info']['isTeacher']){
    $query_submissions = "SELECT id FROM submissions WHERE post_id = $post_id AND user_id = $user_id";
    $result_submissions = mysqli_query($cn, $query_submissions);
    $isSubmitted = mysqli_num_rows($result_submissions)==0 ? false : true;
  }
?>
<div class="col-12">
  <a href="post.php?post_id=<?php echo $post_id?>" class="text-decoration-none text-bg-light" id="post-link-<?php echo $post_id?>">
    <div class="card">
    <div class="card-header d-flex justify-content-between">
      
        <?php if($isAssignment): ?>
          <span class='badge text-bg-warning'>Assignment</span> 
          <small class="date <?php echo ($post['due'] < $date && !$_SESSION['user_info']['isTeacher'] && !$isSubmitted) ? 'text-danger' : NULL ?>"><?php echo date("Y-m-d H:i:s", $post['due']);?></small>
        <?php else:?>
          <span class='badge text-bg-primary'>Material</span>
        <?php endif; ?>
    </div>
    <div class="card-body d-flex justify-content-between align-items-start">
      <div>
        <h5 class="card-title"><?php echo $post['title']?></h5>
        <p class="card-text"><?php echo nl2br(htmlspecialchars($post['body'])) ?></p>

        <!-- display files -->
        <?php
          $query_files = "SELECT * FROM post_files WHERE post_id = $post_id";
          $result = mysqli_query($cn, $query_files);
          if(mysqli_num_rows($result) > 0){?>
          <small class="my-3 text-body-tertiary">
          <?php  echo mysqli_num_rows($result) ?>
          files attached</small>
          <?php }?>
      </div>

      <?php if($_SESSION['user_info']['isTeacher']):?>
      <!-- more btn -->
      <div class="btn-group dropdown w-auto">
        <button type="button" class="btn text-dark rounded-5 p-0 outline-none" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical fs-5"></i>
        </button>
        <ul class="dropdown-menu z-3"> 
            <li>
                <button class="dropdown-item" 
                    onclick="event.preventDefault(); showModal('<?php echo $post_id; ?>', 'edit')">
                    Edit
                </button>
            </li>
            <li>
                <button class="dropdown-item" 
                    onclick="event.preventDefault(); showModal('<?php echo $post_id; ?>', 'delete')">
                    Delete
                </button>
            </li>
        </ul>
    </div>
    <?php endif;?>
    </div>
    <div class="card-footer">
      <small> <?php echo date('M d, Y', $post['date']) ?></small>
    </div>

    <!-- alert badge -->
    <?php
    if(isset($isSubmitted) && !$isSubmitted): ?>
      <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
        <span class="visually-hidden">alert</span>
      </span>
    <?php endif; ?>
    </div>
  </a>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="edit-<?php echo $post_id;?>" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down modal-dialog-scrollable">
        <div class="modal-content">
            <form action="/controllers/posts/update.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <input class="modal-title form-control w-50 fs-5 me-4" name="title" placeholder="Title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    <?php if($isAssignment):?>
                      <input type="number" class="form-control w-auto" name="marks" id="marks-<?php echo $post_id; ?>" placeholder="Total marks" min="0" value="<?php echo $post['marks']; ?>" required>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea name="body" class="form-control mb-3" rows="7" placeholder="Content here..."><?php echo htmlspecialchars($post['body']); ?></textarea>
                    <input type="file" class="form-control mb-3" name="files[]" multiple>

                    <?php if($isAssignment):?>
                      <label for="duedate" id="duelabel-<?php echo $post_id; ?>">Due on:</label>
                      <div class="d-flex" id="duedate-<?php echo $post_id; ?>">
                          <input type="date" name="duedate" class="form-control w-auto" value="<?php echo date('Y-m-d', $post['due']); ?>">
                          <input type="time" name="duetime" class="form-control w-auto" value="<?php echo date('H:i', $post['due']); ?>">
                      </div>
                    <?php endif?>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-bs-dismiss="modal" type="button">Go back</button>
                    <input class="btn btn-success" type="submit" value="Update">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- delete modal -->
<div class="modal fade" id="delete-<?php echo $post_id; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">
                Are you sure you want to delete this class?
            </h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <form action="/controllers/posts/delete.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $post_id; ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
        </div>
    </div>
</div>
<?php endforeach?>
</div>  
</div>

<!-- right bar -->
<div class="col-md-4 d-none d-md-block">

  <?php if($class_info['owner_id'] == $user_id):?>
  <div class="card mb-5">
    <div class="card-header">
      
    <h5 class="card-title m-0">Code</h5>
    </div>
    <div class="card-body d-flex justify-content-center align-items-center">
      <p class="card-text fs-1 d-inline m-1" id="class-id"><?php echo $class_id?></p>
      <button class="btn copy-btn" onclick="copyToClipboard()"><i class="bi bi-copy fs-5"></i></button>
    </div>
  </div>
  <?php endif;?>
  
  <div class="card">
    <div class="card-header">
      Upcoming
    </div>
    <div class="card-body pt-0">
      <?php 
      $date = strval($date);
      $query = "SELECT * FROM posts WHERE class_id = '$class_id' AND 
      marks IS NOT NULL AND due IS NOT NULL AND due >= '$date'
      ORDER BY CAST(due AS UNSIGNED) ASC;";
      $result = mysqli_query($cn, $query);
      $sorted_assignments = mysqli_fetch_all($result, MYSQLI_ASSOC);
      if(!count($sorted_assignments)){
        echo "<p class='text-body-tertiary mt-3 mb-0'>Woohoo! No work due soon</p>";
      }
      foreach($sorted_assignments as $upcoming):
      ?>
        <!-- upcoming assignments -->
        <div class="mt-3">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>

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
    let timeAgo = moment.tz(timestamp.innerText, "Asia/Kuala_Lumpur").fromNow();
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

function showModal(postId, type) {
    var link = document.getElementById('post-link-' + postId);

    // prevent navigation
    link.addEventListener('click', function(event) {
        event.preventDefault();
    });

    // Show modal
    var modalId = '#' + type + '-' + postId;
    var modal = new bootstrap.Modal(document.querySelector(modalId));
    modal.show();
}

</script>
<?php
}
require_once '../template/layout.php';