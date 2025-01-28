<?php
include 'func.php';
include 'cors.php';

// Set headers
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get the raw DELETE data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (isset($data['id'])) {
        // Validate API key
        $apiKey = validateApiKey($conn);
        $organization_id = getOrgIDbyApiKey($conn, $apiKey);

        if (!$organization_id) {
            $response = ['status' => false, 'message' => 'Invalid organization ID.'];
            echo json_encode($response);
            exit;
        }

        // Sanitize input
        $id = htmlspecialchars(strip_tags($data['id']));

        // Prepare the DELETE statement
        $stmt = $conn->prepare("DELETE FROM menu_sub_category WHERE id = ? AND organization_id = ?");
        $stmt->bind_param("ss", $id, $organization_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $response = ['status' => true, 'message' => 'Subcategory deleted successfully.'];
        } else {
            $response = ['status' => false, 'message' => 'Failed to delete subcategory or subcategory not found.'];
        }

        $stmt->close();
    } else {
        $response = ['status' => false, 'message' => 'Invalid input. Required field: id.'];
    }
} else {
    $response = ['status' => false, 'message' => 'Invalid request method.'];
}

$conn->close();
echo json_encode($response);
?>
