<?php
require_once 'db.php';
require_once './code_format.php';

// Validate and sanitize query parameters
$questionId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$language = isset($_GET['language']) ? strtolower(trim($_GET['language'])) : null;

if (!$questionId || !$language) {
    http_response_code(400);
    exit("❌ Missing question ID or language.");
}

// Fetch answer from database
$query = "SELECT answer FROM answerss WHERE questionid = ? AND language = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $questionId, $language);
$stmt->execute();
$result = $stmt->get_result();

// Format and output the solution
if ($row = $result->fetch_assoc()) {
    $solution = $row['answer'];

    switch ($language) {
        case 'cpp':
        case 'c++':
            $solution = code_format::cpp($solution);
            break;
        case 'python':
            $solution = code_format::python($solution);
            break;
        case 'php':
            $solution = code_format::php($solution);
            break;
    }

    header("Content-Type: text/plain");
    echo $solution;
} else {
    echo "⚠️ No solution found for question ID $questionId and language '$language'.";
}

$stmt->close();
$conn->close();
?>
