<?php
// course.php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['course_id'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = $_GET['course_id'];

// Retrieve course details
$query = "SELECT * FROM courses WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

if (!$course) {
    echo "Course not found.";
    exit();
}

// Retrieve modules for this course
$modulesQuery = "SELECT * FROM modules WHERE course_id = ? ORDER BY sort_order ASC";
$stmt = $conn->prepare($modulesQuery);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$modulesResult = $stmt->get_result();
$modules = [];
while ($row = $modulesResult->fetch_assoc()) {
    $modules[] = $row;
}
$stmt->close();

// Retrieve student's current progress for this course
$progressQuery = "SELECT completed FROM student_progress WHERE student_id = ? AND course_id = ?";
$stmt = $conn->prepare($progressQuery);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$progressResult = $stmt->get_result();
if ($progressRow = $progressResult->fetch_assoc()) {
    $currentProgress = $progressRow['completed'];
} else {
    $currentProgress = 0;
    $insertQuery = "INSERT INTO student_progress (student_id, course_id, completed) VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($insertQuery);
    $stmtInsert->bind_param("iii", $user_id, $course_id, $currentProgress);
    $stmtInsert->execute();
    $stmtInsert->close();
}
$stmt->close();

// Total modules count (for progress threshold calculation)
$totalModules = count($modules);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($course['course_name']); ?> – Course Overview</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Roboto', sans-serif; }
    nav { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .hero {
      background: linear-gradient(to right, #4f46e5, #3b82f6);
      color: white;
      padding: 2rem 1rem;
    }
    .card {
      background: white;
      border-radius: 0.5rem;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .card:hover { transform: translateY(-5px); }
    .accordion-header { cursor: pointer; }
    .accordion-content { display: none; }
    .accordion-content.active { display: block; }
  </style>
  <script>
    function toggleAccordion(headerElem) {
      const content = headerElem.nextElementSibling;
      const icon = headerElem.querySelector('svg');
      content.classList.toggle("active");
      icon.classList.toggle("rotate-180");
    }
  </script>
</head>
<body class="bg-gray-100">
  <!-- Navigation Bar -->
  <nav class="bg-white">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex justify-between items-center py-4">
        <a href="dashboard.php" class="text-xl font-bold text-gray-800">Tutoring</a>
        <div class="hidden md:flex space-x-4">
          <a href="dashboard.php" class="py-2 px-3 text-gray-700 hover:text-gray-900">Dashboard</a>
          <a href="course.php?course_id=<?= $course_id ?>" class="py-2 px-3 text-gray-700 hover:text-gray-900">Course</a>
        </div>
        <div>
          <a href="logout.php" class="py-2 px-3 bg-red-500 text-white rounded hover:bg-red-600 transition">Logout</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero text-center">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($course['course_name']); ?></h1>
      <?php if (!empty($course['description'])): ?>
        <p class="text-lg"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Badge Section -->
  <?php
    // Determine which badge to award based on progress percentage.
    // Assuming $currentProgress is stored as a percentage (0 to 100).
    $badge = '';
    $badgeImage = '';
    if ($currentProgress >= 80) {
        $badge = 'Pro Badge';
        $badgeImage = 'pro_badge.png'; // Replace with your actual image path.
    } elseif ($currentProgress >= 60) {
        $badge = 'Intermediate Badge';
        $badgeImage = 'intermediate_badge.png';
    } elseif ($currentProgress >= 20) {
        $badge = 'Beginner Badge';
        $badgeImage = 'beginner_badge.png';
    } else {
        $badge = 'No badge yet. Complete more modules to earn a badge!';
    }
  ?>
  <section class="badge-section max-w-7xl mx-auto my-10 px-4 text-center">
    <h2 class="text-3xl font-bold mb-4">Your Progress Badge</h2>
    <?php if ($currentProgress >= 20): ?>
      <div class="badge inline-block p-6 bg-white shadow-lg rounded-full">
        <img src="<?php echo $badgeImage; ?>" alt="<?php echo $badge; ?>" class="h-24 mx-auto">
        <p class="text-xl font-semibold mt-4"><?php echo $badge; ?></p>
        <p class="text-gray-600">Progress: <?php echo round($currentProgress); ?>%</p>
      </div>
    <?php else: ?>
      <p class="text-gray-600"><?php echo $badge; ?></p>
    <?php endif; ?>
  </section>

  <!-- Course Modules (Accordion Style) -->
  <div class="max-w-7xl mx-auto my-10 px-4">
    <?php if (count($modules) > 0): ?>
      <?php foreach ($modules as $module): ?>
        <div class="mb-6 border rounded overflow-hidden">
          <!-- Accordion Header -->
          <div class="accordion-header bg-blue-500 text-white py-4 px-6 flex justify-between items-center" onclick="toggleAccordion(this)">
            <span class="text-xl font-semibold"><?php echo htmlspecialchars($module['module_name']); ?></span>
            <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
          <div class="accordion-content hidden p-6">
            <?php if (!empty($module['module_description'])): ?>
              <p class="mb-4 text-gray-700"><?php echo nl2br(htmlspecialchars($module['module_description'])); ?></p>
            <?php endif; ?>

            <?php
            // Get chapters for this module
            $chaptersQuery = "SELECT * FROM chapters WHERE module_id = ? ORDER BY sort_order ASC";
            $stmt = $conn->prepare($chaptersQuery);
            $stmt->bind_param("i", $module['id']);
            $stmt->execute();
            $chaptersResult = $stmt->get_result();
            $chapters = [];
            while ($row = $chaptersResult->fetch_assoc()) {
                $chapters[] = $row;
            }
            $stmt->close();
            ?>

            <?php if (count($chapters) > 0): ?>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($chapters as $chapter): ?>
                  <div class="card p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800"><?php echo htmlspecialchars($chapter['chapter_name']); ?></h3>
                    <?php if (!empty($chapter['chapter_description'])): ?>
                      <p class="text-gray-600 mb-3"><?php echo nl2br(htmlspecialchars($chapter['chapter_description'])); ?></p>
                    <?php endif; ?>

                    <?php
                    // Get lessons for this chapter
                    $lessonsQuery = "SELECT * FROM lessons WHERE chapter_id = ? ORDER BY sort_order ASC";
                    $stmt = $conn->prepare($lessonsQuery);
                    $stmt->bind_param("i", $chapter['id']);
                    $stmt->execute();
                    $lessonsResult = $stmt->get_result();
                    $lessons = [];
                    while ($row = $lessonsResult->fetch_assoc()) {
                        $lessons[] = $row;
                    }
                    $stmt->close();
                    ?>
                    <?php if (count($lessons) > 0): ?>
                      <div class="mb-2">
                        <h4 class="font-medium">Lessons:</h4>
                        <ul class="list-disc pl-5">
                          <?php foreach ($lessons as $lesson): ?>
                            <li class="mb-1">
                              <a href="lesson.php?lesson_id=<?= $lesson['id'] ?>" class="text-blue-600 hover:underline">
                                <?php echo htmlspecialchars($lesson['lesson_title']); ?>
                              </a>
                              <?php if (!empty($lesson['video_url'])): ?>
                                <a href="<?php echo htmlspecialchars($lesson['video_url']); ?>" target="_blank" class="text-blue-500 hover:underline">Watch Video</a>
                              <?php endif; ?>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php else: ?>
                      <p class="text-gray-500">No lessons available in this chapter.</p>
                    <?php endif; ?>

                    <?php
                    // Get all quizzes for this chapter
                    $quizQuery = "SELECT * FROM quizzes WHERE chapter_id = ? ORDER BY sort_order ASC";
                    $stmt = $conn->prepare($quizQuery);
                    $stmt->bind_param("i", $chapter['id']);
                    $stmt->execute();
                    $quizResult = $stmt->get_result();
                    $quizzes = [];
                    while ($row = $quizResult->fetch_assoc()) {
                        $quizzes[] = $row;
                    }
                    $stmt->close();
                    ?>
                    <?php if (count($quizzes) > 0): ?>
                      <div>
                        <h4 class="font-medium mb-2">Quizzes:</h4>
                        <ul class="list-disc pl-5">
                          <?php foreach ($quizzes as $quiz): ?>
                            <li>
                              <a href="quiz.php?quiz_id=<?= $quiz['id'] ?>" class="text-blue-600 hover:underline">
                                <?php echo htmlspecialchars($quiz['quiz_title']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php endif; ?>

                    <!-- Coding Environment Integration -->
                    <div class="mt-4">
                      <a href="welcome.php?course_id=<?= $course_id ?>&chapter_id=<?= $chapter['id'] ?>" class="bg-purple-500 hover:bg-purple-600 text-white py-2 px-3 rounded transition">
                        Enter Coding Environment
                      </a>
                    </div>

                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p class="text-gray-600">No chapters available for this module.</p>
            <?php endif; ?>

            <!-- Module Completion Button -->
            <?php
            // Each module contributes equally. Calculate the threshold percentage for this module.
            $moduleThreshold = ceil(($module['sort_order'] / $totalModules) * 100);
            ?>
            <div class="mt-4">
              <?php if ($currentProgress >= $moduleThreshold): ?>
                <span class="bg-green-500 text-white py-2 px-3 rounded">Module Completed</span>
              <?php else: ?>
                <a href="complete_module.php?course_id=<?= $course_id ?>&module_id=<?= $module['id'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded transition">
                  Mark Module Completed
                </a>
              <?php endif; ?>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-gray-600">No modules available for this course.</p>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <footer class="bg-white py-4 mt-10 shadow-inner">
    <div class="max-w-7xl mx-auto text-center text-gray-600">
      &copy; <?php echo date("Y"); ?> Tutoring. All rights reserved.
    </div>
  </footer>
</body>
</html>
