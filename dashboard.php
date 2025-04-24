<?php
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
    /* Custom fade-in animation for tab content */
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
      <h2 class="text-4xl font-bold text-gray-800">Student Dashboard</h2>
      <p class="text-gray-600 mt-2">View and manage your courses.</p>
    </div>
    
    <!-- Tab Navigation -->
    <nav class="mb-10 border-b border-gray-300">
      <ul class="flex justify-center space-x-6">
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
        <!-- New Tab: Talk to Teachers -->
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="talk-to-teachers">Talk to Teachers</a>
        </li>
      </ul>
    </nav>
    
    <!-- Tab Contents -->
    <div class="space-y-10">
      <!-- My Courses Tab -->
      <div id="my-courses" class="tab-content fade-in">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">Enrolled Courses</h3>
          <!-- Table for enrolled courses -->
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-blue-600 text-white">
                <tr>
                  <th class="px-4 py-3 text-left">Course Name</th>
                  <th class="px-4 py-3 text-left">Progress</th>
                  <th class="px-4 py-3 text-left">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php if (!empty($progress_data)): ?>
                  <?php foreach ($progress_data as $course): ?>
                    <tr class="hover:bg-gray-100 transition-colors duration-300">
                      <td class="px-4 py-3">
                        <?= htmlspecialchars($course['course_name']) ?>
                        <?php if ($course['completed'] == 100): ?>
                          <span class="text-xl animate-bounce" title="Course Completed!">🎉</span>
                        <?php endif; ?>
                      </td>
                      <td class="px-4 py-3">
                        <?= htmlspecialchars($course['completed']) ?>%
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                          <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: <?= htmlspecialchars($course['completed']) ?>%;"></div>
                        </div>
                      </td>
                      <td class="px-4 py-3 space-x-2">
                        <a href="course.php?course_id=<?= $course['course_id'] ?>" class="inline-block bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded transition duration-300">
                          Continue Course
                        </a>
                        <a href="drop_course.php?course_id=<?= $course['course_id'] ?>" onclick="return confirm('Are you sure you want to drop this course?');" class="inline-block bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition duration-300">
                          Drop Course
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="px-4 py-3 text-center text-gray-500">No enrolled courses found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- New Courses Tab -->
      <div id="new-courses" class="tab-content" style="display: none;">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">New Courses</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-blue-600 text-white">
                <tr>
                  <th class="px-4 py-3 text-left">Course Name</th>
                  <th class="px-4 py-3 text-left">Description</th>
                  <th class="px-4 py-3 text-left">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php if (!empty($available_courses)): ?>
                  <?php foreach ($available_courses as $course): ?>
                    <tr class="hover:bg-gray-100 transition-colors duration-300">
                      <td class="px-4 py-3"><?= htmlspecialchars($course['course_name']) ?></td>
                      <td class="px-4 py-3"><?= htmlspecialchars($course['description'] ?? 'No description available') ?></td>
                      <td class="px-4 py-3">
                        <a href="enroll_course.php?course_id=<?= $course['id'] ?>" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition duration-300">
                          Enroll
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="px-4 py-3 text-center text-gray-500">No new courses available.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Progress Tracker Tab -->
      <div id="progress-tracker" class="tab-content" style="display: none;">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">Progress Tracker</h3>
          <?php if ($totalCourses > 0): ?>
            <p class="mb-4 text-gray-700">
              Overall Progress: <span class="font-bold text-green-600"><?= $averageProgress ?>%</span> across
              <span class="font-bold text-green-600"><?= $totalCourses ?></span> course(s).
            </p>
            <div class="w-full bg-gray-300 rounded-full h-4 mb-4">
              <div class="bg-green-600 h-4 rounded-full transition-all duration-500" style="width: <?= $averageProgress ?>%;"></div>
            </div>
          <?php else: ?>
            <p class="mb-4 text-gray-700">You are not enrolled in any courses yet.</p>
          <?php endif; ?>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-blue-600 text-white">
                <tr>
                  <th class="px-4 py-3 text-left">Course Name</th>
                  <th class="px-4 py-3 text-left">Progress</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php if (!empty($progress_data)): ?>
                  <?php foreach ($progress_data as $course): ?>
                    <tr class="hover:bg-gray-100 transition-colors duration-300">
                      <td class="px-4 py-3">
                        <?= htmlspecialchars($course['course_name']) ?>
                        <?php if ($course['completed'] == 100): ?>
                          <span class="text-xl animate-bounce" title="Course Completed!">🎉</span>
                        <?php endif; ?>
                      </td>
                      <td class="px-4 py-3">
                        <?= htmlspecialchars($course['completed']) ?>%
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                          <div class="bg-green-600 h-2 rounded-full transition-all duration-500" style="width: <?= htmlspecialchars($course['completed']) ?>%;"></div>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="2" class="px-4 py-3 text-center text-gray-500">No progress data available.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Peer Review Tab -->
      <div id="peer-review" class="tab-content" style="display: none;">
        <div class="p-6 bg-gray-50 rounded-lg shadow text-center">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">Peer Review</h3>
          <p class="mb-4 text-gray-700">Discuss your projects and get feedback from your peers.</p>
          <a href="peer_review.php" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded transition duration-300">
            Go to Peer Review
          </a>
        </div>
      </div>

      <!-- Badges Tab -->
      <div id="badges" class="tab-content" style="display: none;">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">Earned Badges</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-blue-600 text-white">
                <tr>
                  <th class="px-4 py-3 text-left">Course Name</th>
                  <th class="px-4 py-3 text-left">Progress</th>
                  <th class="px-4 py-3 text-left">Badge Earned</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php if (!empty($progress_data)): ?>
                  <?php foreach ($progress_data as $course): ?>
                    <?php 
                      $badge = '';
                      $badgeImage = '';
                      if ($course['completed'] >= 80) {
                          $badge = 'Pro Badge';
                          $badgeImage = 'pro_badge.png';
                      } elseif ($course['completed'] >= 60) {
                          $badge = 'Intermediate Badge';
                          $badgeImage = 'intermediate_badge.png';
                      } elseif ($course['completed'] >= 20) {
                          $badge = 'Beginner Badge';
                          $badgeImage = 'beginner_badge.png';
                      } else {
                          $badge = 'No badge earned';
                      }
                    ?>
                    <tr class="hover:bg-gray-100 transition-colors duration-300">
                      <td class="px-4 py-3"><?= htmlspecialchars($course['course_name']) ?></td>
                      <td class="px-4 py-3"><?= htmlspecialchars($course['completed']) ?>%
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                          <div class="bg-green-600 h-2 rounded-full transition-all duration-500" style="width: <?= htmlspecialchars($course['completed']) ?>%;"></div>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <?php if ($badge != 'No badge earned'): ?>
                          <div class="flex items-center space-x-2">
                            <img src="<?= $badgeImage ?>" alt="<?= $badge ?>" class="h-10">
                            <span><?= $badge ?></span>
                          </div>
                        <?php else: ?>
                          <?= $badge ?>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="px-4 py-3 text-center text-gray-500">No badge data available.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- New Tab: Talk to Teachers -->
      <div id="talk-to-teachers" class="tab-content" style="display: none;">
        <div class="p-6 bg-gray-50 rounded-lg shadow text-center">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">Talk to Teachers</h3>
          <p class="mb-4 text-gray-700">Have a question? Click below to chat with our experts.</p>
          <a href="talk_to_teachers.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition duration-300">
            Chat Now
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Modern Tab Switching Script with Fade-In Animation -->
  <script>
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        tabLinks.forEach(link => link.classList.remove('border-b-2', 'border-blue-600'));
        tabContents.forEach(content => {
          content.style.display = 'none';
          content.classList.remove('fade-in');
        });
        const tabId = this.getAttribute('data-tab');
        const activeTab = document.getElementById(tabId);
        activeTab.style.display = 'block';
        activeTab.classList.add('fade-in');
        this.classList.add('border-b-2', 'border-blue-600');
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
