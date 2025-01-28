<?php
include 'func.php';
include 'cors.php';

// Set headers
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Get the raw PUT data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (isset($data['id']) && (isset($data['name']) || isset($data['category_id']))) {
        // Validate API key
        $apiKey = validateApiKey($conn);
        $organization_id = getOrgIDbyApiKey($conn, $apiKey);

        if (!$organization_id) {
            $response = ['status' => false, 'message' => 'Invalid organization ID.'];
            echo json_encode($response);
            exit;
        }

        // Sanitize inputs
        $id = htmlspecialchars(strip_tags($data['id']));
        $name = isset($data['name']) ? htmlspecialchars(strip_tags($data['name'])) : null;
        $category_id = isset($data['category_id']) ? htmlspecialchars(strip_tags($data['category_id'])) : null;

        // Build SQL query dynamically based on provided fields
        $sql = "UPDATE menu_sub_category SET ";
        $params = [];
        $types = "";

        if ($name) {
            $sql .= "name = ?, ";
            $params[] = $name;
            $types .= "s";
        }

        if ($category_id) {
            $sql .= "category_id = ?, ";
            $params[] = $category_id;
            $types .= "s";
        }

        // Remove trailing comma and add WHERE clause
        $sql = rtrim($sql, ', ') . " WHERE id = ? AND organization_id = ?";
        $params[] = $id;
        $params[] = $organization_id;
        $types .= "ss";

        // Prepare and execute the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $response = ['status' => true, 'message' => 'Subcategory updated successfully.'];
        } else {
            $response = ['status' => false, 'message' => 'Failed to update subcategory or no changes were made.'];
        }

        $stmt->close();
    } else {
        $response = ['status' => false, 'message' => 'Invalid input. Required fields: id and at least one of name or category_id.'];
    }
} else {
    $response = ['status' => false, 'message' => 'Invalid request method.'];
}

$conn->close();
echo json_encode($response);
?>
