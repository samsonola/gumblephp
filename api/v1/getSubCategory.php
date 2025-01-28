<?php
include 'func.php';
include 'cors.php';

// Set headers
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validate API key
    $apiKey = validateApiKey($conn);
    $organization_id = getOrgIDbyApiKey($conn, $apiKey);

    if (!$organization_id) {
        $response = ['status' => false, 'message' => 'Invalid organization ID.'];
        echo json_encode($response);
        exit;
    }

    if (isset($_GET['category_id'])) {
        // Fetch subcategories for a specific category ID
        $category_id = htmlspecialchars(strip_tags($_GET['category_id']));
        
        $stmt = $conn->prepare("SELECT id, name FROM menu_sub_category WHERE organization_id = ? AND category_id = ?");
        $stmt->bind_param("ss", $organization_id, $category_id);
    } else {
        // Fetch all subcategories
        $stmt = $conn->prepare("SELECT id, name FROM menu_sub_category WHERE organization_id = ?");
        $stmt->bind_param("s", $organization_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $subcategories = [];
        while ($row = $result->fetch_assoc()) {
            $subcategories[] = $row;
        }
        $response = ['status' => true, 'subcategories' => $subcategories];
    } else {
        $response = ['status' => false, 'message' => 'No subcategories found.'];
    }

    $stmt->close();
} else {
    $response = ['status' => false, 'message' => 'Invalid request method.'];
}

// Close the database connection
$conn->close();

// Output the response in JSON format
echo json_encode($response);

?>
