<?php
// badges.php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query to get enrolled courses and progress data
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Earned Badges</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Your Earned Badges</h1>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg">
        <thead class="bg-blue-500 text-white">
          <tr>
            <th class="px-4 py-2 text-left">Course Name</th>
            <th class="px-4 py-2 text-left">Progress (%)</th>
            <th class="px-4 py-2 text-left">Badge Earned</th>
          </tr>
        </thead>
</html>
