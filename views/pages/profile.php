<?php
$title = "Profile";
function get_content(){
require_once "../../controllers/connection.php";
if(isset($_GET['user_id']) && $_GET['user_id'] == $_SESSION['user_info']['id']){
    if(isset($_GET['class'])){
        $class_id =$_GET['class'];
        header("Location: profile.php?class=$class_id");
    }else{
        header("Location: profile.php");

    }
}
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_info']['id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($cn, $query);
$user = mysqli_fetch_assoc($result);
?>
<link rel="stylesheet" href="../../assets/styles/style.css">

<div class="container py-5 mt-5">
<div class="row justify-content-center gap-5 align-items-center">
    <div class="col-xl-2 col-lg-3 col-md-4 col-6">
        <img class="rounded-circle object-fit-cover" width="200px" height="200px" src="<?php echo $user['pfp'] ?>" alt="">
    </div>
    <div class="col-md-4 col-sm-6">
        <?php if(!isset($_GET['edit'])):?>
            <h2><?php echo $user['name'] ?></h2>
            <p class="fs-5"><?php echo $user['email']?></p>
            <p>
                <?php
                if($user['isTeacher']){
                    echo 'Teaching ';
                    $query_classes = "SELECT id FROM classes WHERE owner_id = $user_id";
                }else{
                    echo 'Enrolled in ';
                    $query_classes = "SELECT class_id FROM students WHERE user_id = $user_id";
                }
                $result_classes = mysqli_query($cn, $query_classes);
                echo mysqli_num_rows($result_classes);
                ?> classes
            </p>
            <!-- edit profile and password -->
            <?php if(!isset($_GET['user_id'])):?>
            <a href="profile.php?edit=true" class="btn btn-outline-primary w-auto">Edit profile</a>
            <a class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#editPassword">Edit Password</a>

            <!-- Modal -->
            <div class="modal fade" id="editPassword" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitleId">Edit password</h5>
                            <button type="button"class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="/controllers/users/update_password.php" method="POST">
                        <div class="modal-body">
                                <input type="hidden" name="id" value="<?php echo $user_id?>">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="floatingPassword" placeholder="Current Password" name="currentPassword" required>
                                    <label for="floatingPassword">Current Password</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="floatingNewPassword" placeholder="New Password" name="newPassword" required>
                                    <label for="floatingNewPassword">New Password</label>
                                </div>                                    
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="floatingNewPassword2" placeholder="Repeat New Password" name="newPassword2" required>
                                    <label for="floatingNewPassword2">Repeat New Password</label>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary">Save</button>
                        </div>
                        </form>

                    </div>
                </div>
            </div>
            <?php endif;?>

        <?php else:?>
            <form action="/controllers/users/update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $user['id']?>">

                <div class="input-group mb-3">
                <label class="input-group-text" for="inputGroupFile01">Profile picture</label>
                <input type="file" name="image" class="form-control" id="inputGroupFile01">
                </div>

                <div class="form-floating">
                    <input type="text" class="form-control mb-3" id="floatingUsername" placeholder="Name" name="name" value="<?php echo $user['name']?>">
                    <label for="floatingUsername">Name</label>
                </div>
                <input type="email" name="email" value="<?php echo $user['email'] ?>" placeholder="Email" class="form-control mb-3" disabled>
                
                <a href="profile.php" class="btn btn-outline-warning">Go back</a>
                <input type="submit" class="btn btn-success">
            </form>
        <?php endif;?>
    </div>

    <hr>
    <?php if(!$user['isTeacher']):?>
    <div class="col-lg-11 shadow-sm rounded-3 p-4">
        <h3>Attendance</h3>
        <hr>
        <form action="profile.php" method="GET">
            <input type="hidden" name="user_id" value="<?php echo $user_id?>">
        <select class="form-select" name="class" onchange="this.form.submit()">
            <?php
            $query_classes = "SELECT students.class_id AS id, classes.name FROM students 
            INNER JOIN classes on students.class_id = classes.id
            WHERE user_id = $user_id";
            $result_classes = mysqli_query($cn, $query_classes);
            $classes = mysqli_fetch_all($result_classes, MYSQLI_ASSOC);
            $class_id = isset($_GET['class']) ? $_GET['class'] : $classes[0]['id'];
            foreach ($classes as $class){ ?>
                <option value="<?php echo $class['id']?>" <?php echo ($class['id'] == $class_id) ? 'selected' : NULL ?>>
                    <?php echo $class['name']?>
                </option>
            <?php } ?>
        </select>
        </form>
        <?php
        $query = "SELECT * FROM attendance WHERE user_id = '$user_id' AND class_id = '$class_id'";
        $result = mysqli_query($cn, $query);

        $attendance_data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $date = date('Y-m-d', $row['date']);
            $attendance_data[$date] = $row['status'];
        }

        $json_array = array();
        foreach ($attendance_data as $date => $total_present) {
            $json_array[] = array(
                "x" => $date,
                "value" => $total_present
            );
        }

        $json_data = json_encode($json_array, JSON_PRETTY_PRINT);

        $json_file = '../../controllers/attendance/user_attendance.json';
        file_put_contents($json_file, $json_data);?>

            <div id="container"></div>
    </div>
    <?php endif;?>
</div>
</div>

<script src="https://cdn.anychart.com/releases/8.12.0/js/anychart-core.min.js"></script>
<script src="https://cdn.anychart.com/releases/8.12.0/js/anychart-calendar.min.js"></script>
<script src="https://cdn.anychart.com/releases/8.12.0/js/anychart-data-adapter.min.js"></script>
<script>
anychart.onDocumentReady(function () {
    anychart.data.loadJsonFile("/controllers/attendance/user_attendance.json",
        (data) => chart.data(data)
    );
    const chart = anychart.calendar();
    chart.container("container");
    chart.listen("chartDraw", function () {
        document.getElementById("container").style.height =
        chart.getActualHeight() + 5 + "px";
    });
    chart
    .days()
    .spacing(4)
    .stroke(false)
    .noDataStroke(false)
    .noDataHatchFill(false);

    chart.tooltip().format(function () {
        const value = this.getData("value");
        // return article name or default text
        return value==1 ? "Present" : "Absent";
    });
    
    // configure a custom color scale
    var customColorScale = anychart.scales.ordinalColor();
    customColorScale.ranges([
    {equal: 0, color: '#EB3232'},
    {equal: 1, color: '#0F984D'},
    ]);

    // set the custom color scale
    chart.colorScale(customColorScale);

    // hide the color legend
    chart.colorRange(false);
    chart.draw();
});
</script>
<?php }
require_once '../template/layout.php'; ?>