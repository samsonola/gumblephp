<?php

// Define the API endpoint (replace with the correct URL of your test API)
$apiEndpoint = 'http://localhost/gumblephp/api/v1/addOrders.php';  // Replace with actual API URL
$date = time();
// Sample input data for the POST request (same structure as the previous one)
$data = [
    'order' => [
        'last_name' => 'Doe',
        'first_name' => 'John',
        'date' => $date,
        'table_number' => '12',
        'executed_by' => 'admin',
        'organization_id' => 'org123',
    ],
    'order_items' => [
        [
            'menu_name' => 'Pizza',
            'price' => 15.99,
            'status' => 'Pending',
        ],
        [
            'menu_name' => 'Burger',
            'price' => 9.99,
            'status' => 'Pending',
        ]
    ]
];

// Convert the data array to JSON format
$jsonData = json_encode($data);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Get the response as a string
curl_setopt($ch, CURLOPT_POST, true); // Make the request a POST request
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Attach the data to the request
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json', // Set content type to JSON
    'Authorization: Bearer PK_IUVifQHwl1idkrpPepyM4yeVjO9' // Set the Authorization header (replace with your actual API key)
]);

// Execute the cURL request
$response = curl_exec($ch);

// Check for cURL errors
if ($response === false) {
    echo json_encode(['status' => false, 'message' => 'cURL error: ' . curl_error($ch)]);
} else {
    // Decode the JSON response from the API
    $responseData = json_decode($response, true);
     echo $response;
    // Output the response
    // echo json_encode(['status' => true, 'response' => $responseData]);
}

// Close the cURL session
curl_close($ch);

?>
