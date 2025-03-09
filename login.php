<?php
session_start();
// Configuration
$apiKey = 'TFZNe5onqZ97HNmY3T6OwVzvypbZ99daMiRx5Pax'; // Replace with your Firebase API Key
$projectId = 'codingmania-4fec0'; // Replace with your Firebase Project ID
$collection = 'users'; // Your Firestore collection name
$baseURL = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/$collection";

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to find and verify user
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
            $docHashedPassword = $fields['password']['stringValue'] ?? '';

            // Check if the email matches and then verify the password
            if ($docEmail === $email) {
                // Verify the entered password against the hashed password
                if (password_verify($password, $docHashedPassword)) {
                    return true; // Login successful
                } else {
                    return false; // Password does not match
                }
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
    $remember = isset($_POST['remember-me']); // Check if 'Remember Me' is checked

    $error = "Incorrect information"; // Custom error message

    // Check if the email and password match an existing user
    if (findUser($apiKey, $baseURL, $email, $password)) {
        // Set session for the user
        $_SESSION['username'] = $email;

        // Retrieve existing remembered credentials
        setcookie("username", $email, time() + (30 * 24 * 60 * 60), "/");

        // Set a cookie if "Remember Me" is checked
        /*if ($remember) {
            $rememberedEmails = isset($_COOKIE['remembered_emails']) ? json_decode($_COOKIE['remembered_emails'], true) : [];
        } // Ensure it's an array and add the new email if it's not already in the list
       /* if (!in_array($email, $rememberedEmails)) {
            $rememberedEmails[] = $email;
        }*/
        // Ensure it's an array and add new credentials only if they are not already stored
        if ($remember) {
            $rememberedCredentials[$email] = base64_encode($password); // Store encoded password for security
            setcookie("remembered_credentials", json_encode($rememberedCredentials), time() + (86400 * 30), "/"); // Store for 30 days
        }
    

        header('Location: welcome.php'); // Redirect to the dashboard
        exit();
    } else {
        header('Location: default.php?error=' . urlencode($error));
        exit();
    }
}
?>
