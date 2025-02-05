<?php
// peer_review.php
include('db.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Process new forum post submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["content"])) {
    $content = trim($_POST["content"]);
    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO peer_posts (user_id, content) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $content);
        $stmt->execute();
        $stmt->close();
        // Redirect to avoid form resubmission
        header("Location: peer_review.php");
        exit();
    }
}

// Retrieve all forum posts (most recent first)
$query = "SELECT pp.id, pp.content, pp.created_at, u.email 
          FROM peer_posts pp 
          JOIN users u ON pp.user_id = u.id 
          ORDER BY pp.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Peer Review Forum</title>
  <!-- TailwindCSS CDN for styling -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto p-6 bg-white mt-10 shadow-md rounded">
    <h1 class="text-3xl font-bold text-center mb-4">Peer Review Forum</h1>
    
    <!-- Form to post a new message -->
    <div class="mb-6">
      <h2 class="text-xl font-semibold mb-2">Post a New Message</h2>
      <form method="post" action="peer_review.php">
        <textarea name="content" rows="4" class="w-full p-2 border rounded" placeholder="Enter your question, code snippet, or feedback here..." required></textarea>
        <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
          Post
        </button>
      </form>
    </div>
    
    <!-- Display existing posts -->
    <div>
      <h2 class="text-xl font-semibold mb-4">Recent Posts</h2>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($post = $result->fetch_assoc()): ?>
          <div class="mb-4 p-4 bg-gray-50 border rounded">
            <div class="mb-2">
              <span class="font-bold text-gray-800"><?= htmlspecialchars($post["email"]) ?></span>
              <span class="text-sm text-gray-500"><?= htmlspecialchars($post["created_at"]) ?></span>
            </div>
            <p class="text-gray-700"><?= nl2br(htmlspecialchars($post["content"])) ?></p>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-gray-600">No posts yet. Be the first to share your thoughts!</p>
      <?php endif; ?>
    </div>
    
    <div class="mt-4 text-center">
      <a href="dashboard.php" class="text-blue-500 hover:underline">Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
