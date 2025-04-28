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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
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
    </div>
    
    <!-- Tab Navigation -->
    <nav class="mb-10 border-b border-gray-300">
      <ul class="flex justify-center space-x-6">
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300 border-b-2 border-blue-600" data-tab="my-courses">My Courses</a>
        </li>
        <li>
          <a href="#" class="tab-link inline-block py-2 px-4 font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300" data-tab="student-progress">Student Progress</a>
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
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">My Courses</h3>
          
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead class="bg-blue-600 text-white">
                <tr>
                  <th class="px-4 py-3 text-left">Course Name</th>
                  <th class="px-4 py-3 text-left">Description</th>
                  <th class="px-4 py-3 text-left">Students</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php if (!empty($taught_courses)): ?>
                  <?php foreach ($taught_courses as $course): ?>
                    <tr class="hover:bg-gray-100 transition-colors duration-300">
                      <td class="px-4 py-3 font-medium"><?= htmlspecialchars($course['course_name']) ?></td>
                      <td class="px-4 py-3"><?= htmlspecialchars($course['description'] ?? 'No description') ?></td>
                      <td class="px-4 py-3">
                        <?php 
                          $student_count = 0;
                          foreach ($enrollment_stats as $stat) {
                              if ($stat['course_id'] == $course['id']) {
                                  $student_count = $stat['total_students'];
                                  break;
                              }
                          }
                          echo $student_count;
                        ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="px-4 py-3 text-center text-gray-500">You are not teaching any courses yet.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Student Progress Tab -->
      <div id="student-progress" class="tab-content">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">Student Progress Overview</h3>
          
          <?php if (!empty($enrollment_stats)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
              <?php foreach ($enrollment_stats as $stat): ?>
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                  <h4 class="font-semibold text-lg mb-2"><?= htmlspecialchars($stat['course_name']) ?></h4>
                  <div class="space-y-2">
                    <div>
                      <span class="font-medium">Total Students:</span> <?= $stat['total_students'] ?>
                    </div>
                    <div>
                      <span class="font-medium">Average Progress:</span> <?= round($stat['avg_progress'], 2) ?>%
                      <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= round($stat['avg_progress'], 2) ?>%;"></div>
                      </div>
                    </div>
                    <div>
                      <span class="font-medium">Completed:</span> <?= $stat['completed_count'] ?> students
                    </div>
                    <a href="course_progress.php?course_id=<?= $stat['course_id'] ?>" class="inline-block mt-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition duration-300">
                      View Details
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-gray-500">No student progress data available.</p>
          <?php endif; ?>
          
          <div class="mt-6 p-4 bg-white rounded-lg shadow border border-gray-200">
            <h4 class="font-semibold text-lg mb-3">Top Performing Students</h4>
            <div class="overflow-x-auto">
              <table class="min-w-full">
                <thead class="bg-green-600 text-white">
                  <tr>
                    <th class="px-4 py-2 text-left">Student</th>
                    <th class="px-4 py-2 text-left">Course</th>
                    <th class="px-4 py-2 text-left">Progress</th>
                    <th class="px-4 py-2 text-left">Last Activity</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <?php if (!empty($top_students)): ?>
                    <?php foreach ($top_students as $student): ?>
                      <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2"><?= htmlspecialchars($student['username']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($student['course_name']) ?></td>
                        <td class="px-4 py-2">
                          <?= $student['completed'] ?>%
                          <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-green-600 h-2 rounded-full" style="width: <?= $student['completed'] ?>%;"></div>
                          </div>
                        </td>
                        <td class="px-4 py-2"><?= date('M j, Y', strtotime($student['last_accessed'])) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="px-4 py-2 text-center text-gray-500">No student data available.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Peer Forum Tab -->
      <div id="peer-forum" class="tab-content">
        <div class="p-6 bg-gray-50 rounded-lg shadow">
          <h3 class="text-4xl font-bold text-purple-700 text-center mb-8">💬 Peer Review Forum</h3>

          <!-- New Post Form -->
          <div class="mb-10">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Post Something</h2>
            <form method="post" action="teacher_dashboard.php#peer-forum" enctype="multipart/form-data">
              <div class="mb-4">
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Select Course:</label>
                <select name="course_id" id="course_id" class="w-full border border-purple-300 rounded px-3 py-2" onchange="location = 'teacher_dashboard.php?course_id=' + this.value + '#peer-forum';">
                  <?php foreach ($taught_courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= $course['id'] == $selected_course_id ? 'selected' : '' ?>><?= htmlspecialchars($course['course_name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <textarea name="content" rows="4" class="w-full p-4 border border-purple-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-400" placeholder="Share your question, feedback, or a code snippet..." required></textarea>
              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload File (C++, Java, Python, PHP, TXT):</label>
                <input type="file" name="file" class="w-full border border-purple-300 rounded px-3 py-2" />
              </div>
              <button type="submit" class="mt-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold px-5 py-2 rounded">Post</button>
            </form>
          </div>

          <!-- All Posts for Selected Course -->
          <div>
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Recent Posts (<?= htmlspecialchars($selected_course_name) ?>)</h2>
            <?php if (!empty($posts)): ?>
              <div class="space-y-4">
              <?php foreach ($posts as $post): ?>
                <div class="p-5 bg-gray-50 border border-purple-200 rounded-lg shadow">
                  <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-purple-700">👤 <?= htmlspecialchars($post['email']) ?></span>
                    <span class="text-sm text-gray-500">🕒 <?= htmlspecialchars($post['created_at']) ?></span>
                  </div>
                  <p class="text-gray-800 whitespace-pre-line mb-2"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                  <?php if (!empty($post['file_path'])): ?>
                    <a href="uploads/<?= htmlspecialchars($post['file_path']) ?>" target="_blank" class="text-blue-600 hover:underline">📎 Download Attached File</a>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p class="text-gray-600">No posts yet for this course. Be the first to share your thoughts!</p>
            <?php endif; ?>
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
