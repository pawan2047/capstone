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
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="progress-tracker">Progress Tracker</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="peer-review">Peer Review</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="badges">Badges</a>
        </li>
      </ul>
    </nav>

    <!-- Tab Contents -->
    <!-- Initially, only the My Courses tab is displayed; the rest are hidden -->
    
    <!-- My Courses Tab -->
    <div id="my-courses" class="tab-content fade-in">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Enrolled Courses</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg">
          <thead class="bg-blue-500 text-white">
            <tr>
              <th class="px-4 py-2 text-left">Course Name</th>
              <th class="px-4 py-2 text-left">Progress (%)</th>
              <th class="px-4 py-2 text-left">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php if (!empty($progress_data)): ?>
              <?php foreach ($progress_data as $course): ?>
                <tr class="hover:bg-gray-100 transition-colors duration-300">
                  <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['completed']) ?>%</td>
                  <td class="px-4 py-2 space-x-2">
                    <!-- Continue Course button -->
                    <a href="course.php?course_id=<?= $course['course_id'] ?>" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded transition duration-300">
                      Continue Course
                    </a>
                    <!-- Drop Course button -->
                    <a href="drop_course.php?course_id=<?= $course['course_id'] ?>" onclick="return confirm('Are you sure you want to drop this course?');" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition duration-300">
                      Drop Course
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="px-4 py-2 text-center text-gray-500">No enrolled courses found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- New Courses Tab -->
    <div id="new-courses" class="tab-content" style="display: none;">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">New Courses</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg">
          <thead class="bg-blue-500 text-white">
            <tr>
              <th class="px-4 py-2 text-left">Course Name</th>
              <th class="px-4 py-2 text-left">Description</th>
              <th class="px-4 py-2 text-left">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php if (!empty($available_courses)): ?>
              <?php foreach ($available_courses as $course): ?>
                <tr class="hover:bg-gray-100 transition-colors duration-300">
                  <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['description'] ?? 'No description available') ?></td>
                  <td class="px-4 py-2">
                    <a href="enroll_course.php?course_id=<?= $course['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded transition duration-300">
                      Enroll
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="px-4 py-2 text-center text-gray-500">No new courses available.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Progress Tracker Tab -->
    <div id="progress-tracker" class="tab-content" style="display: none;">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Progress Tracker</h2>
      <?php if ($totalCourses > 0): ?>
        <p class="mb-4 text-gray-700">Overall Progress: <span class="font-bold text-green-600"><?= $averageProgress ?>%</span> across <span class="font-bold text-green-600"><?= $totalCourses ?></span> course(s).</p>
      <?php else: ?>
        <p class="mb-4 text-gray-700">You are not enrolled in any courses yet.</p>
      <?php endif; ?>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg">
          <thead class="bg-blue-500 text-white">
            <tr>
              <th class="px-4 py-2 text-left">Course Name</th>
              <th class="px-4 py-2 text-left">Progress (%)</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php if (!empty($progress_data)): ?>
              <?php foreach ($progress_data as $course): ?>
                
