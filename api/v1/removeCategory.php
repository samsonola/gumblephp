<?php

include 'func.php';
include 'cors.php';

// Set headers
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get raw DELETE data
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['category_ids']) && is_array($data['category_ids'])) {
        $apiKey = validateApiKey($conn);
        $organization_id = getOrgIDbyApiKey($conn, $apiKey);

        // Convert category_ids to a comma-separated list
        $category_ids = implode(",", array_map('intval', $data['category_ids']));

        // Delete the categories
        $stmt = $conn->prepare("DELETE FROM menu_category WHERE organization_id = ? AND id IN ($category_ids)");
        $stmt->bind_param("s", $organization_id);

        if ($stmt->execute()) {
            $response = ['status' => true, 'message' => 'Category(ies) deleted successfully.'];
        } else {
            $response = ['status' => false, 'message' => 'Failed to delete category(ies).'];
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
