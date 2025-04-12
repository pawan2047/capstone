<?php
// dashboard.php
include('db.php');
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query to get enrolled courses (My Courses)
$query = "SELECT c.id AS course_id, c.course_name, sp.completed 
          FROM student_progress sp 
          JOIN courses c ON sp.course_id = c.id 
          WHERE sp.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$progress_data = [];
while ($row = $result->fetch_assoc()) {
    $progress_data[] = $row;
}
$stmt->close();

// Query to get available courses (New Courses)
$query2 = "SELECT * FROM courses 
           WHERE id NOT IN (SELECT course_id FROM student_progress WHERE student_id = ?)";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$available_courses = [];
while ($row = $result2->fetch_assoc()) {
    $available_courses[] = $row;
}
$stmt2->close();

// Calculate overall progress for the Progress Tracker tab
$totalCourses = count($progress_data);
$totalProgress = 0;
foreach ($progress_data as $course) {
    $totalProgress += $course['completed'];
}
$averageProgress = ($totalCourses > 0) ? round($totalProgress / $totalCourses, 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard</title>
  <!-- TailwindCSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom modern fade-in animation */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .fade-in {
      animation: fadeIn 0.5s ease-out;
    }
  </style>
</head>
<body class="bg-gradient-to-r from-gray-100 to-gray-200 min-h-screen">
  <div class="max-w-6xl mx-auto mt-10 p-8 bg-white rounded-lg shadow-xl">
    <header class="text-center mb-8">
      <h1 class="text-4xl font-bold text-gray-800">Student Dashboard</h1>
      <p class="text-gray-600 mt-2">Manage your courses, track progress, and more.</p>
    </header>
    
    <!-- Tab Navigation -->
    <nav class="mb-6 border-b border-gray-300">
      <ul class="flex justify-center space-x-4">
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="my-courses">My Courses</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="new-courses">New Courses</a>
        </li>
        <li
