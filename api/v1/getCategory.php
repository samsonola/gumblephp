<?php

include 'func.php';
include 'cors.php';

// Set headers
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validate API key
    $apiKey = validateApiKey($conn);
    $organization_id = getOrgIDbyApiKey($conn, $apiKey);

    if (isset($_GET['category_id'])) {
        // Fetch a specific category (id and name only)
        $category_id = htmlspecialchars(strip_tags($_GET['category_id']));

        $stmt = $conn->prepare("SELECT id, name FROM menu_category WHERE organization_id = ? AND id = ?");
        $stmt->bind_param("ss", $organization_id, $category_id);
    } else {
        // Fetch all categories (id and name only)
        $stmt = $conn->prepare("SELECT id, name FROM menu_category WHERE organization_id = ?");
        $stmt->bind_param("s", $organization_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        $response = ['status' => true, 'categories' => $categories];
    } else {
        $response = ['status' => false, 'message' => 'No categories found.'];
    }

    $stmt->close();
} else {
    $response = ['status' => false, 'message' => 'Invalid request method.'];
}

$conn->close();
echo json_encode($response);

?>
