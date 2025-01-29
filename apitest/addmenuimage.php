<?php

// API endpoint
$apiEndpoint = 'http://localhost/gumblephp/api/v1/addMenu';

// API key
$apiKey = 'PK_IUVifQHwl1idkrpPepyM4yeVjO9';

// Data to send
$data = [
    'names' => 'Sample Menu Item',
    'price' => '500',
    'added_by' => 'Admin',
    'descriptions' => 'Delicious sample menu item',
    'category_id' => '1',
    'sub_category_id' => '2',
    'discount_price' => '450',
    'is_taxed' => true,
    'is_available' => true,
    'is_multi_option' => false
];

// Files to upload
$images = [
    'images' => [
        [
            'name' => '11.png',
            'tmp_name' => '11.png',
        ],
        [
            'name' => '12.jpg',
            'tmp_name' => '12.jpg',
        ],
    ]
];

// Prepare the request
$boundary = uniqid();
$delimiter = '-------------' . $boundary;
$headers = [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: multipart/form-data; boundary=' . $delimiter,
];

$body = buildMultipartData($data, $images, $delimiter);

// Send the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Handle the response
if ($response === false) {
    echo 'cURL Error: ' . $error;
} else {
    echo 'API Response: ' . $response;
}

/**
 * Build multipart/form-data body
 */
function buildMultipartData($data, $files, $delimiter)
{
    $eol = "\r\n";
    $body = '';

    // Add text data
    foreach ($data as $key => $value) {
        $body .= "--" . $delimiter . $eol;
        $body .= 'Content-Disposition: form-data; name="' . $key . '"' . $eol . $eol;
        $body .= $value . $eol;
    }

    // Add files
    foreach ($files['images'] as $file) {
        $body .= "--" . $delimiter . $eol;
        $body .= 'Content-Disposition: form-data; name="images[]"; filename="' . $file['name'] . '"' . $eol;
        $body .= 'Content-Type: ' . mime_content_type($file['tmp_name']) . $eol . $eol;
        $body .= file_get_contents($file['tmp_name']) . $eol;
    }

    // End boundary
    $body .= "--" . $delimiter . "--" . $eol;

    return $body;
}
