<?php
$title = "Post";
function get_content(){
require_once "../../controllers/connection.php";
$post_id = $_GET['post_id'];
$user_id = $_SESSION['user_info']['id'];

$query = "SELECT posts.*, classes.owner_id, classes.id AS class_id, users.name AS owner_name FROM posts
INNER JOIN classes ON posts.class_id = classes.id
INNER JOIN users ON classes.owner_id = users.id
WHERE posts.id = $post_id;";
$result = mysqli_query($cn, $query);
$post = mysqli_fetch_assoc($result);

$isAssignment = $post['marks'] != NULL;
$class_id = $post['class_id'];

//check if user is in class and post exists
$query_check = "SELECT user_id FROM students WHERE user_id = '$user_id' AND class_id = '$class_id'";
if(mysqli_num_rows(mysqli_query($cn, $query_check)) == 0 && $post['owner_id'] != $user_id){ ?>
    <script>
        alert("Not found!");
        window.location.replace("/");
    </script>
<?php
    die();
}
?>
<link rel="stylesheet" href="../../assets/styles/style.css">

<div class="container-lg py-5 px-lg-5 mt-5">
<div class="row px-5 justify-content-between">
<div class="<?php echo $isAssignment ? 'col-md-7' : NULL?>">
    
    <h2><?php echo $post['title']?></h2>
    <p class="m-0 text-body-tertiary">Teacher <?php echo $post['owner_name']?> <span class="mx-2">•</span> <?php echo date("M d, Y", $post['date'])?></p>

    <!-- show marks and due date -->
    <?php if($isAssignment): ?>
        <div class="d-flex justify-content-between mt-2">
            <p class="m-0"><?php echo $post['marks'] ?> marks</p>
            <p class="m-0">Due <?php echo  date('M d, Y h:i A', $post['due']) ?></p>
        </div>

    <?php endif?>
    <hr>
    <p><?php echo nl2br(htmlspecialchars($post['body']))?></p>

    <!-- display files -->
    <?php
        $query_files = "SELECT * FROM post_files WHERE post_id = $post_id";
        $result = mysqli_query($cn, $query_files);
        $files = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach($files as $file):
    ?>
    <a class="card px-3 py-2 mt-3 text-decoration-none" href="/assets/public/files/<?php echo $file['file_path']?>"><?php echo $file['file_path']?></a>
    <?php endforeach; ?>

    <!-- display submission -->
    <?php if(isset($_GET['student_id'])){?>
        
        <h5 class="mt-5">Student's files</h5>

        <?php
        $student_id = $_GET['student_id'];
        $query_submission = "SELECT id FROM submissions WHERE post_id = $post_id AND user_id = $student_id";
        $result_submission = mysqli_query($cn, $query_submission);
        if(mysqli_num_rows($result_submission)>0){
            $submission_id = mysqli_fetch_assoc($result_submission)['id'];
            $query_files = "SELECT file_path FROM submission_files WHERE submission_id = $submission_id";
            $result_files = mysqli_query($cn, $query_files);
            $submission_files = mysqli_fetch_all($result_files, MYSQLI_ASSOC);
            foreach($submission_files AS $file):?>
            
            <a class="card px-3 py-2 mt-3 text-decoration-none" href="/assets/public/files/<?php echo $file['file_path']?>"><?php echo $file['file_path']?></a>

        <?php endforeach;
        }else{
            echo 'No submission found';
        }
    }?>
</div>

<?php 
if($isAssignment):
    if($post['owner_id'] == $user_id): ?>

    <!-- students list -->
    <div class="col-md-4">
    <div class="shadow-sm rounded-3 p-4">
        <?php
        $query_students = "SELECT students.*, users.name, users.pfp FROM students
        INNER JOIN users ON students.user_id = users.id
        WHERE class_id = '$class_id'";
        $result_students = mysqli_query($cn, $query_students);
        if(mysqli_num_rows($result_students)==0):?>
            <div class="text-center">
            <p>No students in your class yet!</p>
            <button class="btn btn-primary rounded-pill"> Invite students</button>
            </div>
        <?php else:
        $students = mysqli_fetch_all($result_students, MYSQLI_ASSOC);?>

        <div class="d-flex justify-content-between">
            <h5>Students</h5>
            <h6><?php
            $query_submissions = "SELECT * FROM submissions WHERE post_id = $post_id";
            $result_submissions = mysqli_query($cn, $query_submissions);
            $submissions = [];
            while ($row = mysqli_fetch_assoc($result_submissions)) {
                $submissions[$row['user_id']] = $row;
            }
            echo mysqli_num_rows($result_submissions). ' of '. mysqli_num_rows($result_students) ?> turned in
            </h6>
        </div>
        <hr>
        <div class="list-group list-group-flush">
        <?php foreach($students as $student):?>
            <a href="post.php?post_id=<?php echo $post_id?>&student_id=<?php echo $student['user_id']?>" class="list-group-item list-group-item-action rounded-3 d-flex align-items-center justify-content-between">

            <div class="d-flex align-items-center">
                <img src="<?php echo $student['pfp'] ?>" width="40px" class="rounded-circle me-3" alt="">
                <?php echo $student['name'] ?>
            </div>
            <!-- missing or completed -->
            <?php 
            if (isset($submissions[$student['user_id']])) {
                echo '<span class="badge bg-success">Completed</span>';
            } else {
                echo '<span class="badge bg-danger">Missing</span>';
            }
            ?>
                
            </a>
        <?php endforeach;?>
        </div>
        <?php endif; ?>
    </div>
    </div>
    
    <?php else:?>
    <!-- upload assignment -->
    <div class="col-md-4">
    <div class="shadow-sm rounded-3 p-3 py-5">

    <div class="d-flex justify-content-between align-items-center">
    <h5>Upload your work</h5>
    <?php
    $query_submissions = "SELECT * FROM submissions WHERE post_id = $post_id AND user_id = $user_id";
    $result_submissions = mysqli_query($cn, $query_submissions);
    if (mysqli_num_rows($result_submissions)==0) {
        if($post['due'] < $date){
            echo '<span class="badge bg-danger">Missing</span>';
        }
    } else {
        echo '<span class="badge bg-success">Turned in</span>';
    }
    ?>
    </div>

    <?php if(mysqli_num_rows($result_submissions)==0):?>

    <form action="/controllers/assignments/submit.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?php echo $post_id?>">
        <input type="file" name="files[]" multiple class="form-control my-3">
        
        <input type="submit" class="form-control btn btn-primary">
    </form>
    </div>
    </div>

    <!-- display files -->
    <form action="">
    <?php else: 
        $submission = mysqli_fetch_assoc($result_submissions);
        $submission_id = $submission['id'];
        $query_files = "SELECT * FROM submission_files WHERE submission_id = $submission_id";
        $result = mysqli_query($cn, $query_files);
        $files = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach($files as $file):
        ?>
        <a class="card px-3 py-2 mt-3 text-decoration-none" href="/assets/public/files/<?php echo $file['file_path']?>"><?php echo $file['file_path']?></a>
        <?php endforeach; ?>

        <input type="submit" class="btn btn-outline-primary form-control mt-3" value="Unsubmit">
    </form>
<?php 
    endif;
    endif;
endif;?>
</div>
</div>
<?php
}
require_once '../template/layout.php';