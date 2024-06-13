<?php
$title = "Class";
function get_content(){
$class_id = $_GET['id'];
$user_id = $_SESSION['user_info']['id'];
require_once "../../controllers/connection.php";
?>
<div class="container py-5">
<div class="row py-5 g-4 justify-content-center">
    <div class="col-lg-11">
        <a class="link link-offset-2" href="class.php?id=<?php echo $class_id?>">
            < Back
        </a>
    </div>

    <!-- students -->
    <div class="col-lg-11 shadow-sm rounded-3 p-4">
        <?php
        $query_students = "SELECT students.*, users.name, users.pfp FROM students
        INNER JOIN users ON students.user_id = users.id
        WHERE class_id = '$class_id'";
        $result_students = mysqli_query($cn, $query_students);
        $students = mysqli_fetch_all($result_students, MYSQLI_ASSOC);?>

        <div class="d-flex justify-content-between">
            <h4>Check attendance</h4>
            <h6><?php echo count($students)?> students</h6>
        </div>
        <hr>
        <!-- if there are no students -->
        <?php if(!count($students)):?>
            <p class="text-center">No students in this class yet!</p>
        <?php else:?>

        <div class="table-responsive">
            <table class="table table-hover">
            <form action="/controllers/attendance/add.php" method="POST">

            <thead>
                <tr>
                <th scope="col" class="fw-normal">
                    <input type="checkbox" name="all" onclick="toggle(this)" class="me-3"><label for="all">Select all</label>
                </th>
                <?php
                $pastWeekTimestamps = [];

                for ($i = 0; $i < 7; $i++) {
                    // Get the date for 'i' days ago, set the time to midnight
                    $date = new DateTime();
                    $date->setTime(0, 0, 0);
                    $date->sub(new DateInterval("P{$i}D"));
                    $timestamp = $date->getTimestamp();?>
                    
                    <th scope="col" class="fw-normal"><?php echo date('d M',$timestamp)?></th>
                    
                <?php // Add the timestamp to the array
                    $pastWeekTimestamps[] = $timestamp;
                }?>


                </tr>
            </thead>
            <tbody>
            <?php foreach($students as $student):
                $student_id = $student['user_id']?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <!-- check if attendance has been marked -->
                            <?php
                                $timestamp = $pastWeekTimestamps[0];
                                $query_attendance = "SELECT status FROM attendance 
                                WHERE user_id = $student_id AND class_id = '$class_id' AND date = '$timestamp' AND status = true";
                                $result_attendance = mysqli_query($cn, $query_attendance);
                                $checked = mysqli_fetch_assoc($result_attendance);
                            ?>
                            <input type="checkbox" name="attendance[]" class="me-3" value="<?php echo $student_id?>" <?php echo $checked ? 'checked' : NULL?>>
                            <img src="<?php echo $student['pfp'] ?>" width="35px" class="rounded-circle me-3" alt="">
                            <?php echo $student['name'] ?>
                        </div>
                    </td>
                    <?php foreach($pastWeekTimestamps as $timestamp):
                        $query_attendance = "SELECT status FROM attendance WHERE user_id = $student_id AND class_id = '$class_id' AND date = '$timestamp'";
                        $result_attendance = mysqli_query($cn, $query_attendance);
                        $attendance = mysqli_fetch_assoc($result_attendance);
                        if($attendance != NULL):
                            if($attendance['status']):
                        ?>
                                <td><span class="badge rounded-pill text-bg-success">Present</span></td>
                            <?php else:?>
                                <td><span class="badge rounded-pill text-bg-danger">Absent</span></td>
                        <?php endif; else:?>
                            <td><span class="badge rounded-pill text-bg-secondary">No class</span></td>

                    <?php endif; endforeach;?>
                </tr>
            <?php endforeach;?>
                
                <tr>
                    <input type="hidden" name="class_id" value="<?php echo $class_id?>">
                    <td colspan="8"><input type="submit" class="btn btn-primary" value="Save"></td>
                </tr>
            </tbody>
            </form>
            </table>
        </div>
        <?php endif;?>
        </div>

        <div class="col-lg-11 shadow-sm rounded-3 p-4">
        <?php
        $query = "SELECT * FROM attendance WHERE class_id = '$class_id'";
        $result = mysqli_query($cn, $query);

        $attendance_data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $date = date('Y-m-d', $row['date']);
            if (!isset($attendance_data[$date])) {
                $attendance_data[$date] = array(
                    'total_present' => 0,
                    'class_id' => $row['class_id']
                );
            }
            $attendance_data[$date]['total_present'] += $row['status'];
        }
    
        $json_array = array();
        foreach ($attendance_data as $date => $data) {
            $json_array[] = array(
                "x" => $date,
                "value" => $data['total_present'],
                "link" => 'id=' . $data['class_id'] . '&date=' . strtotime($date)
            );
        }

        $json_data = json_encode($json_array, JSON_PRETTY_PRINT);

        $json_file = '../../controllers/attendance/attendance.json';
        file_put_contents($json_file, $json_data);?>

            <div id="container"></div>

            <?php 
            if(isset($_GET['date'])):
                $date = $_GET['date'];
                $query_present = "SELECT attendance.*,users.id AS user_id, users.name, users.pfp FROM attendance 
                INNER JOIN users ON attendance.user_id = users.id 
                WHERE date = $date AND class_id = '$class_id' AND status = 1";
                $result_present = mysqli_query($cn, $query_present);
                $present = mysqli_fetch_all($result_present, MYSQLI_ASSOC);?>
                <div class="list-group list-group-flush">
                    <h5 class="mt-3">Students present on <?php echo date('M d', $date)?></h5><hr>
                
                    <?php foreach($present as $student):?> 
                        
                        <a href="profile.php?user_id=<?php echo $student['user_id']?>" class="list-group-item list-group-item-action rounded-4 border-0 py-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $student['pfp'] ?>" width="40px" height="40px" class="rounded-circle me-3" alt="">
                                <?php echo $student['name'] ?>
                            </div>
                        </a>

                    <?php endforeach?>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.anychart.com/releases/8.12.0/js/anychart-core.min.js"></script>
<script src="https://cdn.anychart.com/releases/8.12.0/js/anychart-calendar.min.js"></script>
<script src="https://cdn.anychart.com/releases/8.12.0/js/anychart-data-adapter.min.js"></script>
<script>
function toggle(source) {
    const checkboxes = document.getElementsByName('attendance[]');
    for (let i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
}

anychart.onDocumentReady(function () {
    anychart.data.loadJsonFile("/controllers/attendance/attendance.json",
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
    
    chart.colorRange(false);

    chart.draw();

    chart.listen("pointClick", function (e) {
    const link = "attendance.php?" + chart.data().get(e.dataIndex, "link");
    if (link) {
        window.location.replace(link);
    }
    });
});
</script>

<?php }
require_once '../template/layout.php';
?>