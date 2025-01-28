<?php

// Define the API endpoint
$apiUrl = "http://localhost/gumblephp/api/v1/addSubCategory.php"; // Replace with your actual API URL

// Prepare the data to send in the POST request
$data = [
    'category_id' => '123',  // Example category_id
    'name' => 'Sample Sub Category'  // Example name for the sub-category
];

// Convert the data to JSON
$jsonData = json_encode($data);

// Initialize cURL
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);  // Set the API URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // To get the response as a string
curl_setopt($ch, CURLOPT_POST, true);  // We are sending a POST request
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);  // Attach the JSON data to the request
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',  // Content type is JSON
    'Authorization: Bearer PK_IUVifQHwl1idkrpPepyM4yeVjO9'  // Replace with the actual API key for authorization
]);

// Execute the request and get the response
$response = curl_exec($ch);

// Check if there were any errors with the cURL request
if ($response === false) {
    echo $response;
} else {
    // Decode the JSON response
   echo $response;
}

// Close cURL
curl_close($ch);

?>
