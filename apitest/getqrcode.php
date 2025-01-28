<?php
// The API endpoint URL
$apiUrl = 'http://localhost/gumblephp/api/v1/getMenuQR.php'; // Update this with your actual endpoint URL

// Initialize cURL session
$ch = curl_init();

// Set the cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // To get the response as a string
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set a timeout (optional)

// Execute the cURL request and store the response
$response = curl_exec($ch);

// Check if there was an error with the cURL request
if ($response === false) {
    echo json_encode(["error" => "Failed to call the API."]);
} else {
    echo str_replace('\/', '/', $response);
    // Decode the JSON response from the API
    // $data = json_decode($response, true);

    // // Check if the response has the expected structure
    // if (isset($data['status'])) {
    //     // Handle the response accordingly
    //     if ($data['status'] === true) {
    //         echo json_encode([
    //             "message" => "QR code image path received successfully.",
    //             "qr_code_path" => $data['path']
    //         ]);
    //     } else {
    //         echo json_encode(["error" => $data['message']]);
    //     }
    // } else {
    //     echo json_encode(["error" => "Invalid API response."]);
    // }
}

// Close the cURL session
curl_close($ch);
?>
