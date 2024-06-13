<?php
$title = "Home";
function get_content(){
require_once "controllers/connection.php";?>

<link rel="stylesheet" href="assets/styles/style.css">

<div class="container-lg py-5 px-lg-5">
<div class="row g-5 px-xl-5" style="min-height:250px;">

<div class="col-md-4 col-sm-6 px-4">
    <?php 
    // var_dump($_SESSION['user_info']);

    if($_SESSION['user_info']['isTeacher']):?>
        <button class="button w-100 h-100" data-bs-toggle="modal" data-bs-target="#newClass"><h3>+ Create class</h3></button>
    <?php else:?>
        <button class="button w-100 h-100" data-bs-toggle="modal" data-bs-target="#joinClass"><h3>+ Join class</h3></button>
    <?php endif;?>


</div>

<!-- Modal -->
<div
    class="modal fade"
    id="newClass"
    tabindex="-1"
    role="dialog"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Add class
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <form action="/controllers/classes/create.php" method="POST" autocomplete="off">
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingInput" placeholder="Example name" name="name" required>
                        <label for="floatingInput">Class name (required)</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingInput" name="desc" placeholder="My brand new class">
                        <label for="floatingInput">Description</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Close
                    </button>
                    <button type="submit" class="btn btn-info">Submit</button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<div
    class="modal fade"
    id="joinClass"
    tabindex="-1"
    role="dialog"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Enter a class code
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <form action="/controllers/classes/join.php" method="POST" autocomplete="off">
                <div class="modal-body">

                <div class="input-group"> 
                    <input type="text" maxlength="1" class="digit-input" id="digit1" name="code1" required> 
                    <input type="text" maxlength="1" class="digit-input" id="digit2" name="code2" required> 
                    <input type="text" maxlength="1" class="digit-input" id="digit3" name="code3" required> 
                    <input type="text" maxlength="1" class="digit-input" id="digit4" name="code4" required> 
                    <input type="text" maxlength="1" class="digit-input" id="digit5" name="code5" required> 
                    <input type="text" maxlength="1" class="digit-input" id="digit6" name="code6" required> 
                </div>

                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Close
                    </button>
                    <button type="submit" class="btn btn-info">Submit</button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<?php 
$user_id = $_SESSION['user_info']['id'];
if($_SESSION['user_info']['isTeacher']){
    $query = "SELECT classes.name, classes.id, classes.description, users.name AS owner FROM classes 
        INNER JOIN users ON classes.owner_id = users.id
        WHERE classes.owner_id = $user_id
        ORDER BY create_date ASC";
}else{
    $query = "SELECT classes.id, classes.name, classes.description, classes.create_date, classes.owner_id, users.name AS owner
    FROM students
    JOIN classes ON students.class_id = classes.id
    JOIN users ON classes.owner_id = users.id
    WHERE students.user_id = $user_id;";
}

$result = mysqli_query($cn, $query);
$classes = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach($classes as $class):
?>
<div class="col-md-4 col-sm-6 px-4">
    <a class="button ratio ratio-4x3 h-100" href="/views/pages/class.php?id=<?php echo $class['id']?>" id="class-link-<?php echo $class['id']; ?>">
        <div class="d-flex justify-content-between align-items-start flex-column py-2 px-4 pe-3">
            <div class="pt-2 w-100 d-flex justify-content-between">
                <p><?php echo $class['owner']?></p>
                <!-- more btn -->
                <div class="btn-group dropdown w-auto">
                    <button type="button" class="btn text-white rounded-circle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul class="dropdown-menu z-3"> 
                        <?php if($_SESSION['user_info']['isTeacher']):?>
                        <li>
                            <button class="dropdown-item" 
                                onclick="event.preventDefault(); showModal('<?php echo $class['id']; ?>', 'edit')">
                                Edit
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item" 
                                onclick="event.preventDefault(); showModal('<?php echo $class['id']; ?>', 'delete')">
                                Delete
                            </button>
                        </li>
                        <?php else:?>
                        <li>
                            <button class="dropdown-item" 
                                onclick="event.preventDefault(); showModal('<?php echo $class['id']; ?>', 'leave')">
                                Leave
                            </button>
                        </li>
                        <?php endif;?>
                    </ul>
                </div>
            </div>
            <div> 
                <h3><?php echo $class['name']?></h3>
                <p><?php echo $class['description']?></p>
            </div>
        </div>
    </a>
</div>
<?php if($_SESSION['user_info']['isTeacher']):?>
<!-- edit modal -->
<div class="modal fade" id="editClass-<?php echo $class['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Class</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/controllers/classes/update.php" method="POST" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo $class['id']?>">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingInput" name="name" value="<?php echo $class['name']?>" required>
                        <label for="floatingInput">Class name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingInput" name="desc" value="<?php echo $class['description']?>">
                        <label for="floatingInput">Description</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- delete modal -->
<div class="modal fade" id="deleteClass-<?php echo $class['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">
                Are you sure you want to delete this class?
            </h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form action="/controllers/classes/delete.php" method="POST" autocomplete="off">
            <div class="modal-body">
                Type <b><?php echo $class['name']?></b> to confirm
                <input type="hidden" name="id" value="<?php echo $class['id']?>">
                <input type="text" name="confirm" class="form-control mt-3" placeholder="Type <?php echo $class['name']?>">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
        </div>
    </div>
</div>
<?php else:?>
<!-- leave -->
<div class="modal fade" id="leaveClass-<?php echo $class['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">
                Are you sure you want to leave this class?
            </h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body">
            Are you sure you want to leave this class?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <form action="/controllers/classes/leave.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $class['id']; ?>">
                <button type="submit" class="btn btn-warning">Leave</button>
            </form>
        </div>
        </div>
    </div>
</div>
<?php endif;?>
<?php endforeach; ?>


</div>
</div>

<?php
}
require_once 'views/template/layout.php';?>
<script>
const inputs = document.querySelectorAll('.digit-input'); 

inputs.forEach((input, index) => { 
    input.addEventListener('input', () => { 
        if (input.value.length === 1 && index < inputs.length - 1) { 
            inputs[index + 1].focus(); 
        } 
    }); 

    input.addEventListener('keydown', (event) => { 
        if (event.key === "Backspace" && input.value.length === 0 && index > 0) { 
            inputs[index - 1].focus(); 
        } 
    }); 
});

function showModal(classId, type) {
    var classLink = document.getElementById('class-link-' + classId);

    // prevent navigation
    classLink.addEventListener('click', function(event) {
        event.preventDefault();
    });

    // Show modal
    var modalId = '#' + type + 'Class-' + classId;
    var modal = new bootstrap.Modal(document.querySelector(modalId));
    modal.show();
}

</script>