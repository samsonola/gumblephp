<?php

include 'func.php';
include 'cors.php';

// Set headers
header("Content-Type: application/json");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the input
    if (isset($data['name'])) {
         // Validate API key
        $apiKey = validateApiKey($conn);  // Use the validateApiKey function from func.php

        // Check if the organization exists by API key
        $organization_id = getOrgIDbyApiKey($conn, $apiKey);

        // Sanitize the name input
        $name = htmlspecialchars(strip_tags($data['name']));

        // Prepare the SQL statement to insert the menu category
        $stmt = $conn->prepare("INSERT INTO menu_category (organization_id, name) VALUES (?, ?)");
        $stmt->bind_param("ss", $organization_id, $name);

        // Execute the statement
        if ($stmt->execute()) {
            $response = ['status' => true, 'message' => 'Menu category added successfully.'];
        } else {
            // Return error if insertion fails
            $response = ['status' => false, 'message' => 'Failed to add menu category.'];
        }

        // Close the statement
        $stmt->close();
    } else {
        $response = ['status' => false, 'message' => 'Invalid input.'];
    }
} else {
    $response = ['status' => false, 'message' => 'Invalid request method.'];
}

// Close the database connection
$conn->close();

// Output the response in JSON format
echo json_encode($response);

















