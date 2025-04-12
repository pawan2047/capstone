<?php
// dashboard.php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

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
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .tab-content { display: none; animation: fadeIn 0.3s ease-in-out; }
    .active-tab { display: block; }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .shadow-glow {
      box-shadow: 0 4px 20px rgba(147, 51, 234, 0.15);
    }
  </style>
</head>
<body class="bg-gradient-to-tr from-indigo-200 via-purple-200 to-pink-200 min-h-screen font-sans">
  <div class="max-w-7xl mx-auto mt-12 p-10 bg-white rounded-3xl shadow-glow">
    <header class="text-center mb-12">
      <h1 class="text-5xl font-black text-purple-700 drop-shadow">📘 Student Dashboard</h1>
      <p class="text-xl text-gray-600 mt-3">Explore, Learn, and Track Your Progress</p>
    </header>

    <!-- Navigation Tabs -->
    <nav class="mb-10">
      <ul class="flex justify-center flex-wrap gap-6 text-lg font-semibold">
        <li><a href="#" class="tab-link py-2 px-5 rounded-full bg-purple-100 text-purple-800 hover:bg-purple-300 transition" data-tab="my-courses">My Courses</a></li>
        <li><a href="#" class="tab-link py-2 px-5 rounded-full bg-purple-100 text-purple-800 hover:bg-purple-300 transition" data-tab="new-courses">New Courses</a></li>
        <li><a href="#" class="tab-link py-2 px-5 rounded-full bg-purple-100 text-purple-800 hover:bg-purple-300 transition" data-tab="progress-tracker">Progress Tracker</a></li>
        <li><a href="#" class="tab-link py-2 px-5 rounded-full bg-purple-100 text-purple-800 hover:bg-purple-300 transition" data-tab="peer-review">Peer Review</a></li>
        <li><a href="#" class="tab-link py-2 px-5 rounded-full bg-purple-100 text-purple-800 hover:bg-purple-300 transition" data-tab="badges">Badges</a></li>
      </ul>
    </nav>

    <!-- My Courses Tab -->
    <div id="my-courses" class="tab-content active-tab">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Enrolled Courses</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg">
          <thead class="bg-purple-500 text-white">
            <tr>
              <th class="px-4 py-2 text-left">Course Name</th>
              <th class="px-4 py-2 text-left">Progress (%)</th>
              <th class="px-4 py-2 text-left">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php if (!empty($progress_data)): ?>
              <?php foreach ($progress_data as $course): ?>
                <tr>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['completed']) ?>%</td>
                  <td class="px-4 py-2 space-x-2">
                    <a href="course.php?course_id=<?= $course['course_id'] ?>" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded transition">Continue</a>
                    <a href="drop_course.php?course_id=<?= $course['course_id'] ?>" onclick="return confirm('Are you sure you want to drop this course?');" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition">Drop</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="px-4 py-2 text-center text-gray-500">No enrolled courses found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- New Courses Tab -->
    <div id="new-courses" class="tab-content">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Available Courses</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg">
          <thead class="bg-purple-500 text-white">
            <tr>
              <th class="px-4 py-2 text-left">Course Name</th>
              <th class="px-4 py-2 text-left">Description</th>
              <th class="px-4 py-2 text-left">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php if (!empty($available_courses)): ?>
              <?php foreach ($available_courses as $course): ?>
                <tr>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['description'] ?? 'No description') ?></td>
                  <td class="px-4 py-2">
                    <a href="enroll_course.php?course_id=<?= $course['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded transition">Enroll</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="px-4 py-2 text-center text-gray-500">No new courses available.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Progress Tracker Tab -->
    <div id="progress-tracker" class="tab-content">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Progress Overview</h2>
      <p class="mb-4 text-gray-700">You're enrolled in <strong class="text-purple-700"><?= $totalCourses ?></strong> course(s). Average progress: <strong class="text-purple-700"><?= $averageProgress ?>%</strong></p>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg">
          <thead class="bg-purple-500 text-white">
            <tr>
              <th class="px-4 py-2 text-left">Course</th>
              <th class="px-4 py-2 text-left">Progress</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($progress_data as $course): ?>
              <tr>
                <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($course['completed']) ?>%</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Peer Review Tab -->
    <div id="peer-review" class="tab-content">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Peer Review</h2>
      <p class="mb-4 text-gray-700">Exchange feedback with fellow learners.</p>
      <a href="peer_review.php" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded transition">Go to Peer Review</a>
    </div>

    <!-- Badges Tab -->
    <div id="badges" class="tab-content">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Achievements</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg">
          <thead class="bg-purple-500 text-white">
            <tr>
              <th class="px-4 py-2 text-left">Course</th>
              <th class="px-4 py-2 text-left">Progress</th>
              <th class="px-4 py-2 text-left">Badge</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($progress_data as $course): ?>
              <?php 
                $badge = 'No badge';
                if ($course['completed'] >= 80) $badge = '🌟 Pro';
                elseif ($course['completed'] >= 60) $badge = '🔥 Intermediate';
                elseif ($course['completed'] >= 20) $badge = '✨ Beginner';
              ?>
              <tr>
                <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($course['completed']) ?>%</td>
                <td class="px-4 py-2"><?= $badge ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        tabContents.forEach(content => content.classList.remove('active-tab'));
        tabLinks.forEach(link => link.classList.remove('bg-purple-300', 'font-bold'));
        const tabId = this.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active-tab');
        this.classList.add('bg-purple-300', 'font-bold');
      });
    });

    document.addEventListener('DOMContentLoaded', () => {
      const hash = window.location.hash;
      if (hash) {
        const targetTab = document.querySelector(`.tab-link[data-tab="${hash.substring(1)}"]`);
        if (targetTab) {
          targetTab.click();
          return;
        }
      }
      document.querySelector('.tab-link').click();
    });
  </script>
</body>
</html>
