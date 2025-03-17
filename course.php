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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($course['course_name']); ?> – Course Overview</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Roboto', sans-serif; }
    .hero {
      background: linear-gradient(to right, #4f46e5, #3b82f6);
      color: white;
      padding: 2rem 1rem;
    }
    .accordion-header { cursor: pointer; background: #3b82f6; color: white; padding: 1rem; border-radius: 5px; }
    .accordion-content { display: none; padding: 1rem; border: 1px solid #ddd; border-radius: 5px; margin-top: 5px; background: #fff; }
    .accordion-content.active { display: block; }
  </style>
  <script>
    function toggleAccordion(headerElem) {
      const content = headerElem.nextElementSibling;
      content.classList.toggle("active");
    }
  </script>
</head>
<body class="bg-gray-100">
  <!-- Navigation Bar -->
  <nav class="bg-white p-4 shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <a href="dashboard.php" class="text-xl font-bold text-gray-800">Tutoring</a>
      <div>
        <a href="logout.php" class="py-2 px-3 bg-red-500 text-white rounded hover:bg-red-600 transition">Logout</a>
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

  <!-- Course Modules -->
  <div class="max-w-5xl mx-auto my-10 px-4">
    <?php if (count($modules) > 0): ?>
      <?php foreach ($modules as $module): ?>
        <div class="mb-6 border rounded overflow-hidden">
          <!-- Accordion Header -->
          <div class="accordion-header" onclick="toggleAccordion(this)">
            <span class="text-lg font-semibold"><?php echo htmlspecialchars($module['module_name']); ?></span>
          </div>
          <div class="accordion-content">
            <?php if (!empty($module['module_description'])): ?>
              <p class="mb-4 text-gray-700"><?php echo nl2br(htmlspecialchars($module['module_description'])); ?></p>
            <?php endif; ?>

            <!-- Embed welcome.php as a coding environment -->
            <h3 class="text-lg font-semibold mt-4">Coding Challenge</h3>
            <iframe src="welcome.php?module_id=<?= $module['id'] ?>&question=<?= urlencode($module['coding_question'] ?? 'Solve this problem!') ?>"
                width="100%" height="500px" frameborder="0"></iframe>

            <!-- Module Completion Button -->
            <div class="mt-4">
              <button onclick="completeModule(<?= $course_id ?>, <?= $module['id'] ?>, '<?= urlencode($module['coding_question'] ?? '') ?>')" 
                      class="bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded transition">
                Mark as Complete & Code
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-gray-600">No modules available for this course.</p>
    <?php endif; ?>
  </div>

  <!-- JavaScript for module completion -->
  <script>
    function completeModule(courseId, moduleId, question) {
      alert("Module " + moduleId + " completed! Redirecting to coding practice...");
      window.location.href = "welcome.php?module_id=" + moduleId + "&course_id=" + courseId + "&question=" + question;
    }
  </script>

  <!-- Footer -->
  <footer class="bg-white py-4 mt-10 shadow-inner
