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

        <?php
        $student_id = $_GET['student_id'];
        $query_submission = "SELECT * FROM submissions WHERE post_id = $post_id AND user_id = $student_id AND status = 1";
        $result_submission = mysqli_query($cn, $query_submission);
        if(mysqli_num_rows($result_submission)>0){
            $submission = mysqli_fetch_assoc($result_submission);
            $submission_id = $submission['id'];?>

            <div class="d-flex justify-content-between align-items-end mt-5 mb-3">
                <div>
                    <h4>Student's files</h4>
                    <p class="text-body-tertiary m-0">Submitted on <?php echo date('M d, h:i A', $submission['date']) ?></p>
                </div>
                
                <?php if($submission['marks'] == NULL): ?>
                    <form action="/controllers/assignments/grade.php" method="POST">
                        <input type="hidden" value="<?php echo $submission_id?>" name="submission_id">
                        <input type="number" 
                            class="form-control w-auto d-inline mb-2" 
                            min="0" max="<?php echo $post['marks']?>" 
                            name="marks" required>
                        <label for="marks">/ <?php echo $post['marks'] ?></label>
                        <input type="submit" class="btn btn-primary form-control  d-block" value="Grade">
                    </form>
                <?php else:?>
                    <form action="/controllers/assignments/edit_grade.php" method="POST">
                        <input type="hidden" value="<?php echo $submission_id?>" name="submission_id">
                        <p class="m-0"><?php echo $submission['marks']?> / <?php echo $post['marks']?> marks</p>
                        <input type="submit" class="btn btn-outline-primary form-control  d-block" value="Edit grade">
                    </form>
                <?php endif;?>
            </div>
            
            <hr>
            <?php
            $query_files = "SELECT file_path FROM submission_files WHERE submission_id = $submission_id";
            $result_files = mysqli_query($cn, $query_files);
            $submission_files = mysqli_fetch_all($result_files, MYSQLI_ASSOC);
            foreach($submission_files AS $file):?>
            
            <a class="card px-3 py-2 mt-3 text-decoration-none" href="/assets/public/files/<?php echo $file['file_path']?>"><?php echo $file['file_path']?></a>

        <?php endforeach;

        }else{
            echo '<h5 class="my-5">No submission found!</h5>';
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
            <a class="btn btn-primary rounded-pill" href="students.php?id=<?php echo $class_id?>"> Invite students</a>
            </div>
        <?php else:
        $students = mysqli_fetch_all($result_students, MYSQLI_ASSOC);?>

        <div class="d-flex justify-content-between">
            <h5>Students</h5>
            <h6><?php
            $query_submissions = "SELECT * FROM submissions WHERE post_id = $post_id AND status = 1";
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
            <a href="post.php?post_id=<?php echo $post_id?>&student_id=<?php echo $student['user_id']?>" class="list-group-item list-group-item-action rounded-4 border-0 py-3 d-flex align-items-center justify-content-between">

            <div class="d-flex align-items-center">
                <img src="<?php echo $student['pfp'] ?>" width="40px" height="40px" class="rounded-circle me-3" alt="">
                <?php echo $student['name'] ?>
            </div>
            <!-- missing or completed -->
            <?php 
            if (isset($submissions[$student['user_id']])) {
                if($submissions[$student['user_id']]['marks'] == NULL){
                    echo '<span class="badge bg-warning">Pending grade</span>';
                }else{
                    echo '<span class="badge bg-success">Graded</span>';
                }
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
    $submission = mysqli_fetch_assoc($result_submissions);
    $unsubmitted = (isset($submission) && $submission['status'] == 0) ? true : false;

    if (mysqli_num_rows($result_submissions)==0 || $unsubmitted) {
        if($post['due'] < $date){
            echo '<span class="badge bg-danger">Missing</span>';
        }else{
            echo '<span class="badge bg-success">Assigned</span>';
        }
    } else {
        if($submission['marks']!=NULL){
            echo '<span class="badge bg-success">'.$submission['marks'].' marks</span>';

        }else{
            if($submission['date'] > $post['due']){
                echo '<span class="badge bg-warning">Turned in late</span>';  
            }else{
                echo '<span class="badge bg-success">Turned in</span>';
            }

        }
    }
    ?>
    </div>

    <?php if(mysqli_num_rows($result_submissions)==0 || $unsubmitted):?>

    <form action="/controllers/assignments/submit.php" method="POST" enctype="multipart/form-data">
    <?php
    if($unsubmitted):
        $submission_id = $submission['id'];
        $query_files = "SELECT * FROM submission_files WHERE submission_id = $submission_id";
        $result = mysqli_query($cn, $query_files);
        $files = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach($files as $file):
        ?>
        <div class="d-flex mt-3 justify-content-between">
            <a class="card px-3 py-2 w-100 text-decoration-none" href="/assets/public/files/<?php echo $file['file_path']?>"><?php echo $file['file_path']?></a>
            <a href="/controllers/assignments/delete_file.php?id=<?php echo $file['id']?>" class="btn btn-danger"><i class="bi bi-trash-fill"></i></a>
        </div>
        
        <?php endforeach; endif; ?>
        <input type="hidden" name="post_id" value="<?php echo $post_id?>">
        <input type="file" name="files[]" multiple class="form-control my-3">
        
        <input type="submit" class="form-control btn btn-primary">
    </form>
    </div>
    </div>

    <!-- display files -->
    <?php else: ?>
    <form action="/controllers/assignments/unsubmit.php" method="POST">
        <?php $submission_id = $submission['id'];
        $query_files = "SELECT * FROM submission_files WHERE submission_id = $submission_id";
        $result = mysqli_query($cn, $query_files);
        $files = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach($files as $file):
        ?>
        <a class="card px-3 py-2 mt-3 text-decoration-none" href="/assets/public/files/<?php echo $file['file_path']?>"><?php echo $file['file_path']?></a>
        <?php endforeach; ?>
        
        <input type="hidden" name="submission_id" value="<?php echo $submission_id?>">
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