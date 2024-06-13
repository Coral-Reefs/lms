<?php
$title = "Class";
function get_content(){
$class_id = $_GET['id'];
$user_id = $_SESSION['user_info']['id'];
require_once "../../controllers/connection.php";
?>
<link rel="stylesheet" href="/assets/styles/style.css">
<div class="container py-5">
<div class="row py-5 g-4 justify-content-center">
	<div class="col-12">
        <a class="link link-offset-2" href="class.php?id=<?php echo $class_id?>">
            < Back
        </a>
    </div>
	<div class="table-responsive">
	<table class="table table-hover table-bordered align-middle">
		<thead>
			<tr>
				<th></th>
				<?php
				$query_assignments = "SELECT * FROM posts WHERE class_id = '$class_id' AND marks IS NOT NULL";
				$result_assignments = mysqli_query($cn, $query_assignments);
				$assignments = mysqli_fetch_all($result_assignments, MYSQLI_ASSOC);
				foreach($assignments as $assignment):
				?>
				<th class="fw-semibold">
					<small class="text-body-tertiary d-block"><?php echo isset($assignment['due']) ? date('d M',$assignment['due']) : 'No due date'?></small>
					<a href="post.php?post_id=<?php echo $assignment['id']?>" class="link-dark link-offset-1 link-underline link-underline-opacity-0 link-underline-opacity-75-hover">
						<?php echo $assignment['title'] ?>
					</a>
					<hr class="my-2 text-secondary">
					<small class="text-body-tertiary">out of <?php echo $assignment['marks']?></small>
				</th>
				<?php endforeach?>
			</tr>
		</thead>
		<tbody class="table-group-divider">
			
			<?php
			$query_students = "SELECT users.id, users.name, users.pfp FROM students
			INNER JOIN users ON students.user_id = users.id
			WHERE class_id = '$class_id'";
			$result_students = mysqli_query($cn, $query_students);
			$students = mysqli_fetch_all($result_students, MYSQLI_ASSOC);
			if(count($students)==0){
				echo "<tr class='text-center'><td colspan='".count($assignment)."'>No students to show!</td></tr>'";
			}
			foreach($students as $student):
				$student_id = $student['id'];
			?>
			<tr>
				<td>
					<img src="<?php echo $student['pfp']?>" class="me-3" width="30px" height="30px">
					<a href="profile.php?user_id=<?php echo $student_id?>" class="link-dark link-offset-1 link-underline link-underline-opacity-0 link-underline-opacity-75-hover">
						<?php echo $student['name'] ?>
					</a>
				</td>
				<?php foreach($assignments as $assignment):
					$post_id = $assignment['id'];
					$query = "SELECT * FROM submissions WHERE user_id = $student_id AND post_id = $post_id";
					$result = mysqli_query($cn, $query);
					$submission = mysqli_fetch_assoc($result);
					if(mysqli_num_rows($result)==0){
						if($assignment['due'] < $date){?>
							<td><span class="badge bg-danger">Missing</span></td>
					<?php }else{?>
							<td><span class="badge bg-warning">Waiting for submission</span></td>
					<?php }
					}else{
						if($submission['marks']==NULL){?>
							<td><a href="post.php?post_id=<?php echo $post_id?>&student_id=<?php echo $student_id?>">
								<span class="badge bg-warning">Pending Grade</span>
							</a></td>
					<?php }else{?>
							<td><a href="post.php?post_id=<?php echo $post_id?>&student_id=<?php echo $student_id?>" class="text-decoration-none text-success">
								<?php echo $submission['marks']?> / <?php echo $assignment['marks'] ?>
							</a></td>
					<?php }
					}?>
				<?php endforeach;?>
			</tr>
		<?php endforeach?>
		</tbody>
	</table>
	</div>
</div>
</div>
</div>

<?php }
require_once '../template/layout.php';
?>