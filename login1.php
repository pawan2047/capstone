<?php
// Configuration
$apiKey = 'TFZNe5onqZ97HNmY3T6OwVzvypbZ99daMiRx5Pax'; // Replace with your Firebase API Key
$projectId = 'codingmania-4fec0'; // Replace with your Firebase Project ID
$collection = 'users'; // Your Firestore collection name
$baseURL = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/$collection";

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to find user
function findUser($apiKey, $baseURL, $email, $password) {
    $url = "$baseURL?key=$apiKey";
    $options = ['http' => ['method' => 'GET']];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        echo "Error fetching data from Firebase.";
        return false;
    }

    $documents = json_decode($result, true);

    // Check if 'documents' exists in the response
    if (isset($documents['documents'])) {
        foreach ($documents['documents'] as $document) {
            $fields = $document['fields'];
            $docEmail = $fields['email']['stringValue'] ?? '';
            $docPassword = $fields['password']['stringValue'] ?? '';

            // If a match is found, return true immediately
            if ($docEmail === $email && $docPassword === $password) {
                return true;
            }
        }
    }

    // No match was found after checking all documents
    return false;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
   
    $error = "Incorrect information"; // Custom error message

    // Check if the email and password match an existing user
    if (findUser($apiKey, $baseURL, $email, $password)) {
        header('Location: welcome.php'); // Redirect to a welcome page or dashboard if login is successful
        exit();
    } else {
        echo $email;
    echo $password;
        $error = $email;
        header('Location: default.php?error=' . urlencode($error));
        exit();
    }
}
?>
