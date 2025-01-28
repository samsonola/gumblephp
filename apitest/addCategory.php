<?php
// URL of the API endpoint
$apiUrl = 'http://localhost/gumblephp/api/v1/addCategory.php';  // Replace with the actual endpoint URL

// Data to be sent in the POST request
$data = [
    'name' => 'New Menu Category'  // Replace with the desired menu category name
];

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
curl_setopt($ch, CURLOPT_POST, true);  // Use POST request
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  // Send data as JSON
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',  // Specify that the content is JSON
    'Authorization: Bearer PK_39vLkmspkOYy'  // Replace with a valid API key
]);

// Execute the cURL request and get the response
$response = curl_exec($ch);

// Check if any error occurred
if ($response === false) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Handle the response
    if (isset($responseData['status']) && $responseData['status'] === true) {
        echo $response;
    } else {
        echo $response;
    }
}

// Close the cURL session
curl_close($ch);
?>





















