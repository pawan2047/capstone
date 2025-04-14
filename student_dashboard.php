<?php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Initialize variables with default values
$progress_data = [];
$available_courses = [];
$announcements = [];
$recent_messages = [];
$totalCourses = 0;
$averageProgress = 0;
$badgeCounts = [
    'pro' => 0,
    'intermediate' => 0,
    'beginner' => 0
];

// Get enrolled courses with teacher info
$query = "SELECT c.id AS course_id, c.course_name, sp.completed, 
                 u.username AS teacher_name, u.teacher_code
          FROM student_progress sp 
          JOIN courses c ON sp.course_id = c.id
          JOIN users u ON c.teacher_id = u.id
          WHERE sp.student_id = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $progress_data[] = $row;
    }
    $stmt->close();
}

// Get available courses
$query2 = "SELECT c.*, u.username AS teacher_name 
           FROM courses c
           JOIN users u ON c.teacher_id = u.id
           WHERE c.id NOT IN (SELECT course_id FROM student_progress WHERE student_id = ?)";
$stmt2 = $conn->prepare($query2);
if ($stmt2) {
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    while ($row = $result2->fetch_assoc()) {
        $available_courses[] = $row;
    }
    $stmt2->close();
}

// Get announcements for student's enrolled courses
$course_ids = array_column($progress_data, 'course_id');
if (!empty($course_ids)) {
    $placeholders = implode(',', array_fill(0, count($course_ids), '?'));
    $query3 = "SELECT a.*, c.course_name, u.username AS teacher_name 
               FROM announcements a
               JOIN courses c ON a.course_id = c.id
               JOIN users u ON a.teacher_id = u.id
               WHERE a.course_id IN ($placeholders)
               ORDER BY a.created_at DESC
               LIMIT 5";
    $stmt3 = $conn->prepare($query3);
    if ($stmt3) {
        $stmt3->bind_param(str_repeat('i', count($course_ids)), ...$course_ids);
        $stmt3->execute();
        $result3 = $stmt3->get_result();
        while ($row = $result3->fetch_assoc()) {
            $announcements[] = $row;
        }
        $stmt3->close();
    }
}

// Get recent messages between student and teachers
$query4 = "SELECT m.*, u.username AS teacher_name, c.course_name
           FROM messages m
           JOIN users u ON m.teacher_id = u.id
           LEFT JOIN courses c ON m.course_id = c.id
           WHERE m.student_id = ?
           ORDER BY m.created_at DESC
           LIMIT 5";
$stmt4 = $conn->prepare($query4);
if ($stmt4) {
    $stmt4->bind_param("i", $user_id);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    while ($row = $result4->fetch_assoc()) {
        $recent_messages[] = $row;
    }
    $stmt4->close();
}

// Calculate overall progress
$totalCourses = count($progress_data);
$totalProgress = 0;
foreach ($progress_data as $course) {
    $totalProgress += $course['completed'];
}
$averageProgress = ($totalCourses > 0) ? round($totalProgress / $totalCourses, 2) : 0;

// Calculate badge counts
foreach ($progress_data as $course) {
    if ($course['completed'] >= 80) {
        $badgeCounts['pro']++;
    } elseif ($course['completed'] >= 60) {
        $badgeCounts['intermediate']++;
    } elseif ($course['completed'] >= 20) {
        $badgeCounts['beginner']++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard</title>
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
      <h2 class="text-4xl font-bold text-gray-800">Student Dashboard</h2>
      <p class="text-gray-600 mt-2">Track your learning progress and explore new courses.</p>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <div class="bg-blue-100 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-blue-800">Enrolled Courses</h3>
        <p class="text-3xl font-bold text-blue-600 mt-2"><?= $totalCourses ?></p>
      </div>
      <div class="bg-green-100 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-green-800">Overall Progress</h3>
        <p class="text-3xl font-bold text-green-600 mt-2"><?= $averageProgress ?>%</p>
      </div>
      <div class="bg-purple-100 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-purple-800">Earned Badges</h3>
        <p class="text-3xl font-bold text-purple-600 mt-2"><?= array_sum($badgeCounts) ?></p>
      </div>
    </div>
    
    <!-- Tab Navigation -->
    <nav class="mb-10 border-b border-gray-300">
      <ul class="flex justify-center space-x-6">
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300 border-b-2 border-blue-600" data-tab="my-courses">My Courses</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="new-courses">New Courses</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="progress-tracker">Progress</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="badges">Badges</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="peer-review">Peer Review</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="talk-to-teachers">Ask Teacher</a>
        </li>
      </ul>
    </nav>
    
    <!-- Tab Contents -->
    <div class="space-y-10">
      <!-- My Courses Tab -->
      <div id="my-courses" class="tab-content fade-in active">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">My Enrolled Courses</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-blue-600 text-white">
                <tr>
                  <th class="px-4 py-3 text-left">Course</th>
                  <th class="px-4 py-3 text-left">Progress</th>
                  <th class="px-4 py-3 text-left">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php if (!empty($progress_data)): ?>
                  <?php foreach ($progress_data as $course): ?>
                    <tr class="hover:bg-gray-100 transition-colors duration-300">
                      <td class="px-4 py-3">
                        <div class="font-medium"><?= htmlspecialchars($course['course_name']) ?></div>
                        <?php if ($course['completed'] == 100): ?>
                          <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Completed</span>
                        <?php endif; ?>
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center">
                          <span class="w-12"><?= $course['completed'] ?>%</span>
                          <div class="w-full bg-gray-200 rounded-full h-2 ml-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $course['completed'] ?>%;"></div>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3 space-x-2">
                        <a href="course.php?course_id=<?= $course['course_id'] ?>" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition duration-300">
                          Continue
                        </a>
                        <a href="drop_course.php?course_id=<?= $course['course_id'] ?>" onclick="return confirm('Are you sure you want to drop this course?');" class="inline-block bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition duration-300">
                          Drop
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="px-4 py-3 text-center text-gray-500">You haven't enrolled in any courses yet.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- New Courses Tab -->
      <div id="new-courses" class="tab-content">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">Available Courses</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($available_courses)): ?>
              <?php foreach ($available_courses as $course): ?>
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:border-blue-500 transition-colors duration-300">
                  <h4 class="font-bold text-lg mb-2"><?= htmlspecialchars($course['course_name']) ?></h4>
                  <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($course['description'] ?? 'No description available') ?></p>
                  <a href="enroll_course.php?course_id=<?= $course['id'] ?>" class="inline-block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition duration-300">
                    Enroll Now
                  </a>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-span-3 text-center py-8">
                <p class="text-gray-500">No new courses available at this time.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Progress Tracker Tab -->
      <div id="progress-tracker" class="tab-content">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">Your Learning Progress</h3>
          
          <?php if ($totalCourses > 0): ?>
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 mb-6">
              <h4 class="font-semibold text-lg mb-2">Overall Progress</h4>
              <div class="flex items-center justify-between mb-2">
                <span class="font-medium"><?= $averageProgress ?>% Complete</span>
                <span class="text-sm text-gray-600"><?= $totalCourses ?> course(s)</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-green-600 h-3 rounded-full" style="width: <?= $averageProgress ?>%;"></div>
              </div>
            </div>
            
            <div class="space-y-4">
              <h4 class="font-semibold text-lg">Course Breakdown</h4>
              <div class="overflow-x-auto">
                <table class="min-w-full">
                  <thead class="bg-blue-600 text-white">
                    <tr>
                      <th class="px-4 py-2 text-left">Course</th>
                      <th class="px-4 py-2 text-left">Progress</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <?php foreach ($progress_data as $course): ?>
                      <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                        <td class="px-4 py-2">
                          <div class="flex items-center">
                            <span class="w-12"><?= $course['completed'] ?>%</span>
                            <div class="w-full bg-gray-200 rounded-full h-2 ml-2">
                              <div class="bg-green-600 h-2 rounded-full" style="width: <?= $course['completed'] ?>%;"></div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php else: ?>
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 text-center">
              <p class="text-gray-500">You haven't enrolled in any courses yet. Explore our <a href="#new-courses" class="text-blue-600 hover:underline">course catalog</a> to get started!</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Badges Tab -->
      <div id="badges" class="tab-content">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">Your Achievements</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-4 rounded-lg shadow border border-blue-200">
              <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <h4 class="font-semibold">Beginner Badges</h4>
                  <p class="text-2xl font-bold text-blue-600"><?= $badgeCounts['beginner'] ?></p>
                </div>
              </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow border border-green-200">
              <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                  </svg>
                </div>
                <div>
                  <h4 class="font-semibold">Intermediate Badges</h4>
                  <p class="text-2xl font-bold text-green-600"><?= $badgeCounts['intermediate'] ?></p>
                </div>
              </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow border border-purple-200">
              <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-full mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                  </svg>
                </div>
                <div>
                  <h4 class="font-semibold">Pro Badges</h4>
                  <p class="text-2xl font-bold text-purple-600"><?= $badgeCounts['pro'] ?></p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h4 class="font-semibold text-lg mb-4">Your Course Badges</h4>
            <div class="overflow-x-auto">
              <table class="min-w-full">
                <thead class="bg-blue-600 text-white">
                  <tr>
                    <th class="px-4 py-2 text-left">Course</th>
                    <th class="px-4 py-2 text-left">Progress</th>
                    <th class="px-4 py-2 text-left">Badge</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <?php if (!empty($progress_data)): ?>
                    <?php foreach ($progress_data as $course): ?>
                      <?php 
                        $badge = '';
                        $badgeClass = '';
                        if ($course['completed'] >= 80) {
                            $badge = 'Pro Badge';
                            $badgeClass = 'bg-purple-100 text-purple-800';
                        } elseif ($course['completed'] >= 60) {
                            $badge = 'Intermediate Badge';
                            $badgeClass = 'bg-green-100 text-green-800';
                        } elseif ($course['completed'] >= 20) {
                            $badge = 'Beginner Badge';
                            $badgeClass = 'bg-blue-100 text-blue-800';
                        } else {
                            $badge = 'No badge yet';
                            $badgeClass = 'bg-gray-100 text-gray-800';
                        }
                      ?>
                      <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2"><?= htmlspecialchars($course['course_name']) ?></td>
                        <td class="px-4 py-2">
                          <div class="flex items-center">
                            <span class="w-12"><?= $course['completed'] ?>%</span>
                            <div class="w-full bg-gray-200 rounded-full h-2 ml-2">
                              <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $course['completed'] ?>%;"></div>
                            </div>
                          </div>
                        </td>
                        <td class="px-4 py-2">
                          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $badgeClass ?>">
                            <?= $badge ?>
                          </span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="3" class="px-4 py-2 text-center text-gray-500">No badge data available.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Peer Review Tab -->
      <div id="peer-review" class="tab-content">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 text-center">
              <h3 class="text-2xl font-semibold text-gray-800 mb-4">Peer Review</h3>
              <p class="mb-4 text-gray-700">Get feedback on your projects from fellow students.</p>
              <a href="peer_review.php" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded transition duration-300">
                View Peer Reviews
              </a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 text-center">
              <h3 class="text-2xl font-semibold text-gray-800 mb-4">Submit Work</h3>
              <p class="mb-4 text-gray-700">Submit your project for peer review.</p>
              <a href="submit_work.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition duration-300">
                Submit Project
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Talk to Teachers Tab -->
      <div id="talk-to-teachers" class="tab-content">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
              <h3 class="text-2xl font-semibold text-gray-800 mb-4">Ask a Teacher</h3>
              <p class="mb-4 text-gray-700">Have questions about your coursework? Get help directly from instructors.</p>
              <a href="talk_to_teachers.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition duration-300">
                Start Chat
              </a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
              <h3 class="text-2xl font-semibold text-gray-800 mb-4">Recent Messages</h3>
              <?php if (!empty($recent_messages)): ?>
                <div class="space-y-3">
                  <?php foreach ($recent_messages as $message): ?>
                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                      <div class="font-medium">From: <?= htmlspecialchars($message['teacher_name']) ?></div>
                      <div class="text-sm text-gray-600"><?= date('M j, g:i a', strtotime($message['created_at'])) ?></div>
                      <div class="mt-1 text-gray-700 truncate"><?= htmlspecialchars($message['message']) ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-gray-500">No recent messages.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Tab switching functionality
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active classes
        tabLinks.forEach(l => l.classList.remove('border-b-2', 'border-blue-600'));
        tabContents.forEach(c => c.classList.remove('active', 'fade-in'));
        
        // Add active classes to clicked tab
        const tabId = this.getAttribute('data-tab');
        const activeTab = document.getElementById(tabId);
        this.classList.add('border-b-2', 'border-blue-600');
        activeTab.classList.add('active', 'fade-in');
      });
    });

    // Activate first tab by default
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
