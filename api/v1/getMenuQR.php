<?php
// Set content type to JSON for API response
header("Content-Type: application/json");

// Path to the directory where the QR code image is stored
$qrCodeDir = __DIR__ . "/qrcode/"; // Update this path based on where the QR codes are stored
$baseUrl = "http://localhost/gumblephp/api/v1/"; // Update this with your actual base URL

// File name of the saved QR code
$filename = "menu_qr.png";

// Full path to the saved image
$filePath = $qrCodeDir . $filename;

// Check if the file exists on the server
if (file_exists($filePath)) {
    // Return success message with the image path
    echo json_encode([
        "status" => true,
        "message" => "QR code has been generated successfully",
        "path" => $baseUrl . $filename
    ]);
} else {
    // Return error if the file doesn't exist
    echo json_encode(["status" => false, "message" => "QR code could not be generated, please try again."]);
}
?>
