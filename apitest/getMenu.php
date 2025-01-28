<?php
// API endpoint URL
$apiUrl = 'http://localhost/gumblephp/api/v1/getCategory.php';  // Replace with your actual API endpoint URL

// API key for authentication
$apiKey = 'PK_IUVifQHwl1idkrpPepyM4yeVjO9';  // Replace with your actual API key

// Optional menu_id for filtering a specific menu
$menuId = '';  // Leave empty to fetch all menus, or set a specific menu_id like '123'

// Construct the API request URL
$url = $apiUrl . '?api_key=' . urlencode($apiKey);
if (!empty($menuId)) {
    $url .= '&menu_id=' . urlencode($menuId);
}

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects, if any
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL certificate verification (use with caution)
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',  // Specify the request content type as JSON
    'Authorization: Bearer ' . $apiKey  // Pass the API key in the Authorization header
]);

// Execute cURL request and capture the response
$response = curl_exec($ch);

// Check for errors
// if (curl_errno($ch)) {
//     echo 'cURL Error: ' . curl_error($ch);
// } else {
    // Decode the JSON response
    // $responseData = json_decode($response, true);

    // Check the API response status
    // if ($responseData['status']) {
        echo $response;

         // Display the response data
    // } else {
    //     echo $response;  // Display the error message from the API
    // }
// }

// Close cURL session
curl_close($ch);

