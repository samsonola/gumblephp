<?php

include 'func.php';
include 'cors.php';

// Set headers
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Get raw PUT data
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['category_id']) && isset($data['name'])) {
        $apiKey = validateApiKey($conn);
        $organization_id = getOrgIDbyApiKey($conn, $apiKey);

        $category_id = htmlspecialchars(strip_tags($data['category_id']));
        $name = htmlspecialchars(strip_tags($data['name']));

        // Update the category
        $stmt = $conn->prepare("UPDATE menu_category SET name = ? WHERE organization_id = ? AND id = ?");
        $stmt->bind_param("sss", $name, $organization_id, $category_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $response = ['status' => true, 'message' => 'Category updated successfully.'];
        } else {
            $response = ['status' => false, 'message' => 'Failed to update category.'];
        }

        $stmt->close();
    } else {
        $response = ['status' => false, 'message' => 'Invalid input.'];
    }
} else {
    $response = ['status' => false, 'message' => 'Invalid request method.'];
}

$conn->close();
echo json_encode($response);

?>
