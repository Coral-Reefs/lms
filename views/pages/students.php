<?php
$title = "Class";
function get_content(){
$class_id = $_GET['id'];
$user_id = $_SESSION['user_info']['id'];
require_once "../../controllers/connection.php";
?>
<div class="container py-5">
<div class="row py-5 g-4 justify-content-center">
    <div class="col-lg-8">
        <a class="link link-offset-2" href="class.php?id=<?php echo $class_id?>">
            < Back
        </a>
    </div>

    <!-- teacher -->
    <div class="col-lg-8 shadow-sm rounded-3 p-4">
        <h4>Teacher</h4>
    <hr>
    <?php
    $query_teacher = "SELECT classes.owner_id AS id, users.name, users.pfp FROM classes 
    JOIN users ON classes.owner_id = users.id WHERE classes.id = '$class_id'";
    $result_teacher = mysqli_query($cn, $query_teacher);
    $teacher = mysqli_fetch_assoc($result_teacher);
    ?>
    <div class="list-group list-group-flush">
        <a href="profile.php?user_id=<?php echo $teacher['id']?>" class="list-group-item list-group-item-action rounded-4 border-0 py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <img src="<?php echo $teacher['pfp'] ?>" width="40px" height="40px" class="rounded-circle me-3" alt="">
                <?php echo $teacher['name'] ?>
            </div>
        </a>
    </div>
    </div>

        <!-- students -->
    <div class="col-lg-8 shadow-sm rounded-3 p-4">
        <?php
        $query_students = "SELECT students.*, users.name, users.pfp FROM students
        INNER JOIN users ON students.user_id = users.id
        WHERE class_id = '$class_id'";
        $result_students = mysqli_query($cn, $query_students);
        $students = mysqli_fetch_all($result_students, MYSQLI_ASSOC);?>

        <div class="d-flex justify-content-between">
            <h4>Students</h4>
            <h6><?php echo count($students)?> students</h6>
        </div>
        <hr>
        <div class="list-group list-group-flush">
            
        <?php if($_SESSION['user_info']['isTeacher']):?>
        <button href="" class="list-group-item list-group-item-action border-1 rounded-4  py-3 d-flex align-items-center justify-content-center"  data-bs-toggle="modal" data-bs-target="#exampleModal">
            <div class="d-flex align-items-center gap-3 fw-semibold">
            <i class="bi bi-person-plus-fill fs-4"></i> Invite students
            </div>
        </button>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Share join code</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-center align-items-center">
                <p class="fs-1 d-inline m-1" id="class-id"><?php echo $class_id?></p>

                <button class="btn copy-btn" onclick="copyToClipboard()"><i class="bi bi-copy fs-5"></i></button>
            </div>
            </div>
        </div>
        </div>
        <?php endif;?>
        <?php foreach($students as $student):?>
            <a href="profile.php?user_id=<?php echo $student['user_id']?>" class="list-group-item list-group-item-action rounded-4 border-0 py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <img src="<?php echo $student['pfp'] ?>" width="40px" height="40px" class="rounded-circle me-3" alt="">
                    <?php echo $student['name'] ?>
                </div>
                <?php if($_SESSION['user_info']['isTeacher']): ?>
                <form action="/controllers/classes/kick.php" method="POST">
                    <input type="hidden" name="student_id" value="<?php echo $student['id'] ?>">
                    <input type="submit" class="btn btn-outline-danger" value="Kick student">
                </form>
                <?php endif; ?>
            </a>
        <?php endforeach;?>
        </div>
    </div>
</div>
</div>

<script>
function copyToClipboard() {
    var textToCopy = document.getElementById('class-id').innerText;

    if (navigator.clipboard && window.isSecureContext) {
        // Navigator clipboard API method
        navigator.clipboard.writeText(textToCopy).then(function() {
            alert('Text copied to clipboard!');
        }, function(err) {
            alert('Failed to copy text: ', err);
        });
    } else {
        // Fallback method
        var tempTextArea = document.createElement('textarea');
        tempTextArea.value = textToCopy;
        document.body.appendChild(tempTextArea);
        tempTextArea.select();
        try {
            document.execCommand('copy');
            alert('Text copied to clipboard!');
        } catch (err) {
            alert('Failed to copy text: ', err);
        }
        document.body.removeChild(tempTextArea);
    }
}
</script>

<?php }
require_once '../template/layout.php';
?>