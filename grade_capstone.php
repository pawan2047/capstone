<?php
include('db.php');
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['submission_id'])) {
    header("Location: teacher_dashboard.php");
    exit();
}
$submission_id = $_GET['submission_id'];

// Fetch submission details
$stmt = $conn->prepare("SELECT * FROM capstone_projects WHERE id = ?");
$stmt->bind_param("i", $submission_id);
$stmt->execute();
$result = $stmt->get_result();
$submission = $result->fetch_assoc();
$stmt->close();

if (!$submission) {
    echo "Submission not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $approval = $_POST['approval'];  // expected: 'approve' or 'resubmit'
    $comment = $_POST['comment'];
    
    // (Optional) If you have a teacher_comment column, update it; otherwise, you could log it separately.
    // $stmt = $conn->prepare("UPDATE capstone_projects SET teacher_comment = ? WHERE id = ?");
    // $stmt->bind_param("si", $comment, $submission_id);
    // $stmt->execute();
    // $stmt->close();
    
    if ($approval === 'approve') {
        // Update student progress for this student in the corresponding course to 100%
        $student_id = $submission['student_id'];
        $course_id = $submission['course_id'];
        $stmt = $conn->prepare("UPDATE student_progress SET completed = 100 WHERE student_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $student_id, $course_id);
        $stmt->execute();
        $stmt->close();
        
        echo "<script>alert('Final project approved. Student progress updated.'); window.location.href='teacher_dashboard.php';</script>";
    } else {
        echo "<script>alert('Submission marked for resubmission. Ask the student to try again.'); window.location.href='teacher_dashboard.php';</script>";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Final Project - Code Academy</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
t" name="approval" value="approve" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-300">
          Approve
        </button>
        <button type="submit" name="approval" value="resubmit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-300">
          Request Resubmission
        </button>
      </div>
    </form>
  </div>
</body>
</html>
