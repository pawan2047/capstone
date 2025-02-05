<?php
// dashboard.php
include('db.php');
session_start();
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
    /* Custom styles for a smoother tab transition */
    .tab-content {
      display: none;
      animation: fadeIn 0.3s ease-in-out;
    }
    .active-tab {
      display: block;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
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
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600" data-tab="my-courses">My Courses</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600" data-tab="new-courses">New Courses</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600" data-tab="progress-tracker">Progress Tracker</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600" data-tab="peer-review">Peer Review</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600" data-tab="apply-certification">Certification</a>
        </li>
      </ul>
    </nav>

    <!-- Tab Contents -->

    <!-- My Courses Tab -->
    <div id="my-courses" class="tab-content active-tab">
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
                <tr>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['completed']) ?>%</td>
                  <td class="px-4 py-2">
                    <a href="take_course.php?course_id=<?= $course['course_id'] ?>" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded transition duration-150">
                      Continue Course
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
    <div id="new-courses" class="tab-content">
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
                <tr>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['description'] ?? 'No description available') ?></td>
                  <td class="px-4 py-2">
                    <!-- When clicked, enroll_course.php enrolls the user and redirects with #my-courses -->
                    <a href="enroll_course.php?course_id=<?= $course['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded transition duration-150">
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
    <div id="progress-tracker" class="tab-content">
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
                <tr>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($course['completed']) ?>%</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="2" class="px-4 py-2 text-center text-gray-500">No progress data available.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Peer Review Section Tab -->
    <div id="peer-review" class="tab-content">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Peer Review Section</h2>
      <p class="mb-4 text-gray-700">Review your peers’ work and receive feedback on your own projects.</p>
      <div class="text-center">
        <a href="peer_review.php" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded transition duration-150 inline-block">
          Go to Peer Review
        </a>
      </div>
    </div>

    <!-- Apply for Certification Tab -->
    <div id="apply-certification" class="tab-content">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Apply for Certification</h2>
      <p class="mb-4 text-gray-700">If you have met the required course completions, you can apply for certification.</p>
      <div class="text-center">
        <a href="apply_certification.php" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded transition duration-150 inline-block">
          Apply Now
        </a>
      </div>
    </div>

  </div>

  <!-- Tab Switching Script -->
  <script>
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        // Hide all tab contents
        tabContents.forEach(content => content.classList.remove('active-tab'));
        // Remove active styling from all links
        tabLinks.forEach(link => link.classList.remove('border-b-2', 'border-blue-500'));
        // Activate the selected tab's content and add active styling to the clicked link
        const tabId = this.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active-tab');
        this.classList.add('border-b-2', 'border-blue-500');
      });
    });

    // On page load, check if a hash is present in the URL.
    document.addEventListener('DOMContentLoaded', () => {
      const hash = window.location.hash;
      if (hash) {
        const targetTab = document.querySelector(`.tab-link[data-tab="${hash.substring(1)}"]`);
        if (targetTab) {
          targetTab.click();
          return;
        }
      }
      // Default: activate the first tab
      document.querySelector('.tab-link').click();
    });
  </script>
</body>
</html>
