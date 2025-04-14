
<?php
include('db.php');
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// Get teacher info including their teacher_code
$user_id = $_SESSION['user_id'];
$query = "SELECT role, teacher_code FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) die("Error preparing statement: " . $conn->error);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


if ($user['role'] !== 'teacher') {
    header("Location: student_dashboard.php");
    exit();
}


$teacher_code = $user['teacher_code'];


// Get courses taught by this teacher by joining with users table
$query = "SELECT c.* FROM courses c
          JOIN users u ON c.teacher_id = u.id
          WHERE u.teacher_code = ?";
$stmt = $conn->prepare($query);
if (!$stmt) die("Error preparing statement: " . $conn->error);
$stmt->bind_param("s", $teacher_code);
$stmt->execute();
$result = $stmt->get_result();
$taught_courses = [];
while ($row = $result->fetch_assoc()) {
    $taught_courses[] = $row;
}
$stmt->close();


// Get student enrollment statistics for teacher's courses
$enrollment_stats = [];
foreach ($taught_courses as $course) {
    $query = "SELECT
                COUNT(*) as total_students,
                AVG(completed) as avg_progress,
                SUM(CASE WHEN completed = 100 THEN 1 ELSE 0 END) as completed_count
              FROM student_progress
              WHERE course_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) die("Error preparing statement: " . $conn->error);
    $stmt->bind_param("i", $course['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    if ($stats) {
        $stats['course_name'] = $course['course_name'];
        $stats['course_id'] = $course['id'];
        $enrollment_stats[] = $stats;
    }
    $stmt->close();
}


// Get pending assignments to grade for teacher's courses
$query = "SELECT a.id as assignment_id, a.title, a.due_date,
                 c.course_name,
                 COUNT(s.id) as submissions_count
          FROM assignments a
          JOIN courses c ON a.course_id = c.id
          JOIN users u ON c.teacher_id = u.id
          LEFT JOIN submissions s ON a.id = s.assignment_id AND s.grade IS NULL
          WHERE u.teacher_code = ?
          GROUP BY a.id, a.title, a.due_date, c.course_name
          HAVING submissions_count > 0
          ORDER BY a.due_date ASC
          LIMIT 5";
$stmt = $conn->prepare($query);
if (!$stmt) die("Error preparing statement: " . $conn->error);
$stmt->bind_param("s", $teacher_code);
$stmt->execute();
$result = $stmt->get_result();
$pending_assignments = [];
while ($row = $result->fetch_assoc()) {
    $pending_assignments[] = $row;
}
$stmt->close();


// Get recently graded assignments
$query = "SELECT a.title, c.course_name, COUNT(s.id) as graded_count, MAX(s.graded_at) as last_graded
          FROM assignments a
          JOIN courses c ON a.course_id = c.id
          JOIN users u ON c.teacher_id = u.id
          JOIN submissions s ON a.id = s.assignment_id
          WHERE u.teacher_code = ? AND s.grade IS NOT NULL
          GROUP BY a.title, c.course_name
          ORDER BY last_graded DESC
          LIMIT 3";
$stmt = $conn->prepare($query);
if (!$stmt) die("Error preparing statement: " . $conn->error);
$stmt->bind_param("s", $teacher_code);
$stmt->execute();
$result = $stmt->get_result();
$graded_assignments = [];
while ($row = $result->fetch_assoc()) {
    $graded_assignments[] = $row;
}
$stmt->close();


// Get top performing students in teacher's courses
$query = "SELECT u.username, c.course_name, sp.completed, sp.last_accessed
          FROM student_progress sp
          JOIN users u ON sp.student_id = u.id
          JOIN courses c ON sp.course_id = c.id
          JOIN users ut ON c.teacher_id = ut.id
          WHERE ut.teacher_code = ?
          ORDER BY sp.completed DESC, sp.last_accessed DESC
          LIMIT 5";
$stmt = $conn->prepare($query);
if (!$stmt) die("Error preparing statement: " . $conn->error);
$stmt->bind_param("s", $teacher_code);
$stmt->execute();
$result = $stmt->get_result();
$top_students = [];
while ($row = $result->fetch_assoc()) {
    $top_students[] = $row;
}
$stmt->close();


// Peer Review Forum Data
$selected_course_id = $_GET['course_id'] ?? ($taught_courses[0]['id'] ?? null);


// Retrieve posts for the selected course
$posts = [];
if ($selected_course_id) {
    $postQuery = "SELECT pp.id, pp.content, pp.created_at, pp.file_path, u.email FROM peer_posts pp
                  JOIN users u ON pp.user_id = u.id
                  WHERE pp.course_id = ? ORDER BY pp.created_at DESC";
    $postStmt = $conn->prepare($postQuery);
    if ($postStmt) {
        $postStmt->bind_param("i", $selected_course_id);
        $postStmt->execute();
        $result = $postStmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        $postStmt->close();
    }
}


$selected_course_name = 'Selected Course';
foreach ($taught_courses as $course) {
    if ($course['id'] == $selected_course_id) {
        $selected_course_name = $course['course_name'];
        break;
    }
}


// Handle new post with file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content']) && isset($_POST['course_id'])) {
    $content = trim($_POST['content']);
    $selected_course_id = intval($_POST['course_id']);
    $filename = null;


    if (!empty($_FILES['file']['name'])) {
        $allowed_extensions = ['cpp', 'java', 'py', 'php', 'txt'];
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir);
        $original_name = basename($_FILES['file']['name']);
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);


        if (in_array(strtolower($ext), $allowed_extensions)) {
            $filename = uniqid() . "_" . $original_name;
            move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $filename);
        }
    }


    if (!empty($content) && $selected_course_id > 0) {
        $stmt = $conn->prepare("INSERT INTO peer_posts (user_id, content, course_id, file_path) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isis", $user_id, $content, $selected_course_id, $filename);
            $stmt->execute();
            $stmt->close();
            header("Location: teacher_dashboard.php?course_id=$selected_course_id#peer-forum");
            exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Teacher Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .fade-in { animation: fadeIn 0.5s ease-out; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
  </style>
</head>
<body class="bg-gradient-to-r from-gray-200 to-gray-100">
  <!-- Sticky Top Navigation Bar -->
  <header class="sticky top-0 bg-gray-800 text-white flex justify-between items-center px-6 py-4 shadow-lg z-10">
    <h1 class="text-xl font-bold">Code Academy</h1>
    <nav>
      <a href="logout.php" class="text-sm font-semibold hover:text-gray-300 transition-colors duration-300">Logout</a>
    </nav>
  </header>
 
  <div class="max-w-6xl mx-auto mt-16 p-8 bg-white rounded-lg shadow-xl">
    <!-- Page Header -->
    <div class="text-center mb-10">
      <h2 class="text-4xl font-bold text-gray-800">Teacher Dashboard</h2>
      <p class="text-gray-600 mt-2">Manage your courses and students.</p>
      <?php if (!empty($taught_courses)): ?>
        <div class="mt-4">
          <span class="font-medium">Teaching:</span>
          <?php foreach ($taught_courses as $course): ?>
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm ml-2"><?= htmlspecialchars($course['course_name']) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
   
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <div class="bg-blue-100 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-blue-800">Courses Taught</h3>
        <p class="text-3xl font-bold text-blue-600 mt-2"><?= count($taught_courses) ?></p>
      </div>
      <div class="bg-green-100 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-green-800">Total Students</h3>
        <p class="text-3xl font-bold text-green-600 mt-2">
          <?= array_sum(array_column($enrollment_stats, 'total_students')) ?>
        </p>
      </div>
      <div class="bg-purple-100 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-purple-800">Pending Assignments</h3>
        <p class="text-3xl font-bold text-purple-600 mt-2"><?= count($pending_assignments) ?></p>
      </div>
    </div>
   
    <!-- Tab Navigation -->
    <nav class="mb-10 border-b border-gray-300">
      <ul class="flex justify-center space-x-6">
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="my-courses">My Courses</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="student-progress">Student Progress</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="grade-assignments">Grade Assignments</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="peer-forum">Peer Forum</a>
        </li>
      </ul>
    </nav>
   
    <!-- Tab Contents -->
    <div class="space-y-10">
      <!-- My Courses Tab -->
      <div id="my-courses" class="tab-content fade-in active">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-semibold text-gray-800">My Courses</h3>
