<?php
// API endpoint URL
$apiUrl = "http://localhost/gumblephp/api/v1/signIn"; // Change to the actual API URL

// Login credentials
$data = [
    "email" => "realdevolatunde163@gmail.com", // Replace with actual email
    "password" => "12345" // Replace with actual password
];

// API key
$apiKey = "PK_VMqSTZVGndlwEocVwUnkVOypAB0"; // Replace with the actual API key

// Initialize cURL session
$ch = curl_init($apiUrl);

// Convert data array to JSON
$jsonData = json_encode($data);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL error: " . curl_error($ch);
} else {
    // Decode and display the response
//     $decodedResponse = json_decode($response, true);
//     print_r($decodedResponse);
echo $response;
}


// Close cURL session
curl_close($ch);
?>
