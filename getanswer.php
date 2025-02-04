<?php
// Configuration
$apiKey = 'TFZNe5onqZ97HNmY3T6OwVzvypbZ99daMiRx5Pax'; // Replace with your Firebase API Key
$projectId = 'codingmania-4fec0'; // Replace with your Firebase Project ID
$questionId = '6'; // The logical question ID (stored as a field)
$baseURL = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/questions";

// Function to find the correct Firestore document ID
function findQuestionDocumentId($baseURL, $questionId, $apiKey) {
    $url = "$baseURL?key=$apiKey";
    $options = ['http' => ['method' => 'GET']];
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === FALSE) return null;

    $documents = json_decode($result, true);
    if (isset($documents['documents'])) {
        foreach ($documents['documents'] as $document) {
            if ($document['fields']['id']['stringValue'] == $questionId) {
                return basename($document['name']); // Extract the Firestore document ID
            }
        }
    }
    return null;
}

// Step 1: Find the correct document ID
$correctDocId = findQuestionDocumentId($baseURL, $questionId, $apiKey);
if (!$correctDocId) die("No matching question found for ID: $questionId");

// Step 2: Fetch Java answer from answers subcollection
$answersURL = "$baseURL/$correctDocId/answers";

function retrieveJavaAnswer($answersURL, $apiKey) {
    $url = "$answersURL?key=$apiKey";
    $options = ['http' => ['method' => 'GET']];
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === FALSE) return "No Java solution found.";

    $documents = json_decode($result, true);
    if (isset($documents['documents'])) {
        foreach ($documents['documents'] as $document) {
            if (isset($document['fields']['java']['stringValue'])) {
                return $document['fields']['java']['stringValue'];
            }
        }
    }
    return "No Java solution found.";
}

// Step 3: Fetch Java solution
$javaSolution = retrieveJavaAnswer($answersURL, $apiKey);

// Display the answer
echo "<h2>Java Solution for Question ID: " . htmlspecialchars($questionId) . "</h2>";
echo "<pre style='background-color:#f4f4f4; padding:10px; border-radius:5px;'>" . htmlentities($javaSolution) . "</pre>";
?>
