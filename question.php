<?php

// Configuration
$apiKey = 'TFZNe5onqZ97HNmY3T6OwVzvypbZ99daMiRx5Pax'; // Replace with your Firebase API Key
$projectId = 'codingmania-4fec0'; // Replace with your Firebase Project ID
$collection = 'questions'; // Your Firestore collection name
$baseURL = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/$collection";

// Function to retrieve a single question by ID
function retrieveQuestionById($apiKey, $baseURL, $id) {
    $url = "$baseURL?key=$apiKey";
    $options = ['http' => ['method' => 'GET']];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        return null;
    }

    $documents = json_decode($result, true);

    if (isset($documents['documents'])) {
        foreach ($documents['documents'] as $document) {
            $fields = $document['fields'];
            $docId = $fields['id']['stringValue'] ?? '';
            $questionText = $fields['question']['stringValue'] ?? '';

            if ((string)$docId === (string)$id) {
                return ['id' => $docId, 'question' => $questionText];
            }
        }
    }

    return null;
}

//Question id Value
$currentId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Fetch the question with the current ID
$question = retrieveQuestionById($apiKey, $baseURL, $currentId);

if ($question) {
    $_SESSION['current_question'] = $question['question']; // Store in session

    echo '<div><strong>Question ID:</strong> ' . htmlspecialchars($question['id']) . '<br>';
    echo '<strong>Question:</strong> ' . htmlspecialchars($question['question']) . '</div>';
    
    // Hidden input to store question in JavaScript
    echo '<input type="hidden" id="current-question" value="' . htmlspecialchars($question['question']) . '">';
} else {
    echo '<div>No question found for ID ' . htmlspecialchars($currentId) . '.</div>';
}
?>
