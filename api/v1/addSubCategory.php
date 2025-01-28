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
    if (isset($data['name']) && isset($data['category_id'])) {
        // Validate API key
        $apiKey = validateApiKey($conn);  // Use the validateApiKey function from func.php

        // Check if the organization exists by API key
        $organization_id = getOrgIDbyApiKey($conn, $apiKey);

        // If organization ID is not found, return an error
        if (!$organization_id) {
            $response = ['status' => false, 'message' => 'Invalid organization ID.'];
            echo json_encode($response);
            exit;
        }

        // Sanitize the inputs
        $category_id = htmlspecialchars(strip_tags($data['category_id']));
        $name = htmlspecialchars(strip_tags($data['name']));

        // Prepare the SQL statement to insert the menu sub-category
        $stmt = $conn->prepare("INSERT INTO menu_sub_category (organization_id, name, category_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $organization_id, $name, $category_id);

        // Execute the statement
        if ($stmt->execute()) {
            $response = ["status" => true, "message" => "Menu sub-category added successfully and has been assigned to a category of ID $category_id"];
        } else {
            $response = ["status" => false, "message" => "Failed to add menu sub-category."];
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























// include 'func.php';
// include 'cors.php';
// // Set headers

// // Check if the request method is POST
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Get the raw POST data
//     $data = json_decode(file_get_contents('php://input'), true);

//     // Validate the input
//     if (isset($data['organization_id']) && isset($data['name']) && isset($data['category_id'])) {
//         $category_id = htmlspecialchars(strip_tags($data['category_id']));
//         $organization_id = htmlspecialchars(strip_tags($data['organization_id']));
//         $name = htmlspecialchars(strip_tags($data['name']));

//         // Prepare the SQL statement
//         $stmt = $conn->prepare("INSERT INTO menu_sub_category (organization_id, name, category_id) VALUES (?, ?, ?)");
//         $stmt->bind_param("sss", $organization_id, $name, $category_id);

//         // Execute the statement
//         if ($stmt->execute()) {
//             $response = ["status" => true, "message" => "Menu Sub category added successfully and has been assigned to a category of ID $category_id"];
//         } else {
//             $response = ["status" => false, "message" => "Failed to add menu category."];
//         }

//         // Close the statement
//         $stmt->close();
//     } else {
//         $response = ['status' => false, 'message' => 'Invalid input.'];
//     }
// } else {
//     $response = ['status' => false, 'message' => 'Invalid request method.'];
// }

// // Close the database connection
// $conn->close();

// // Output the response in JSON format
// echo json_encode($response);
?>
