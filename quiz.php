
<?php
// quiz.php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['quiz_id'])) {
    header("Location: dashboard.php");
    exit();
}

$quiz_id = $_GET['quiz_id'];

// Retrieve quiz info
$query = "SELECT * FROM quizzes WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
$quiz = $result->fetch_assoc();
$stmt->close();

if (!$quiz) {
    echo "Quiz not found.";
    exit();
}

// Retrieve quiz questions from quiz_questions table
$query = "SELECT * FROM quiz_questions WHERE quiz_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
$stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $total = count($questions);
    foreach ($questions as $q) {
        $qid = $q['id'];
        $correct = $q['correct_option']; // Expected to be 'A', 'B', 'C', or 'D'
        $userAnswer = isset($_POST["answer_$qid"]) ? $_POST["answer_$qid"] : '';
        if ($userAnswer === $correct) {
            $score++;
        }
    }
    $percentage = round(($score / $total) * 100, 2);
    echo "<div class='max-w-4xl mx-auto p-6 bg-white shadow-md mt-10'>";
    echo "<h1 class='text-3xl font-bold mb-4'>Quiz Results</h1>";
    echo "<p>You scored $score out of $total ($percentage%).</p>";
    echo "<a href='course.php?course_id=" . $quiz['course_id'] . "' class='text-blue-500 hover:underline'>Back to Course</a>";
    echo "</div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($quiz['quiz_title']); ?> - Quiz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto p-6 bg-white shadow-md mt-10">
    <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($quiz['quiz_title']); ?></h1>
    <?php if (!empty($quiz['quiz_content'])): ?>
      <p class="mb-6"><?php echo nl2br(htmlspecialchars($quiz['quiz_content'])); ?></p>
    <?php endif; ?>
    <?php if (count($questions) > 0): ?>
      <form method="post">
        <?php foreach ($questions as $index => $q): ?>
          <div class="mb-4">
            <p class="font-semibold"><?php echo ($index + 1) . ". " . htmlspecialchars($q['question_text']); ?></p>
            <label class="block">
              <input type="radio" name="answer_<?php echo $q['id']; ?>" value="A" required>
              A) <?php echo htmlspecialchars($q['option_a']); ?>
            </label>
            <label class="block">
              <input type="radio" name="answer_<?php echo $q['id']; ?>" value="B">
              B) <?php echo htmlspecialchars($q['option_b']); ?>
            </label>
            <label class="block">
              <input type="radio" name="answer_<?php echo $q['id']; ?>" value="C">
              C) <?php echo htmlspecialchars($q['option_c']); ?>
            </label>
            <label class="block">
              <input type="radio" name="answer_<?php echo $q['id']; ?>" value="D">
              D) <?php echo htmlspecialchars($q['option_d']); ?>
            </label>
          </div>
        <?php endforeach; ?>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Submit Quiz</button>
      </form>
    <?php else: ?>
      <p>No questions available for this quiz.</p>
    <?php endif; ?>
  </div>
</body>
</html>

