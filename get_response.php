<?php
// Define your OpenAI API key
$api = include 'api_keys.php';
$apiKey = $api['openai_api_key'];

// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';

// Function to make a request to the OpenAI API
function getOpenAIResponse($prompt) {
    global $apiKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ]);

    $data = json_encode([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => 150,
    ]);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return json_encode(['response' => 'Error: ' . curl_error($ch)]);
    }

    curl_close($ch);

    $responseData = json_decode($response, true);

    if (isset($responseData['error'])) {
        return json_encode(['response' => 'Error: ' . $responseData['error']['message']]);
    }

    return json_encode(['response' => $responseData['choices'][0]['message']['content'] ?? 'No response']);
}

// Output the API response
header('Content-Type: application/json');
echo getOpenAIResponse($prompt);
?>
