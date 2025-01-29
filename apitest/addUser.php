<?php

function generateRandomString($prefix, $totalLength) {
    // Define the characters to use for the random part of the string
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';

    // Calculate the length of the random part
    $randomPartLength = $totalLength - strlen($prefix);

    // Ensure the total length is greater than the prefix length
    if ($randomPartLength <= 0) {
        return $prefix; // If not, return the prefix as is
    }

    // Generate the random part of the string
    for ($i = 0; $i < $randomPartLength; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    // Concatenate the prefix with the random string
    return $prefix . $randomString;
}
// Endpoint URL
$url = "http://localhost/gumblephp/api/v1/signup.php"; // Replace with the actual URL of your signUp endpoint
$getorg = generateRandomString("Gb_", 12);
// Data to send to the endpoint
$data = [
    "firstname" => "Mavin",
    "lastname" => "Ziva",
    "phone" => "08164572111",
    "email" => "realdevolatunde163@gmail.com",
];

// Convert the data to JSON
$jsonData = json_encode($data);

// Initialize cURL
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Content-Length: " . strlen($jsonData)
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL error: " . curl_error($ch);
} else {
    // Display the response from the endpoint
    echo "Response: " . $response;
}

// Close the cURL session
curl_close($ch);
?>
