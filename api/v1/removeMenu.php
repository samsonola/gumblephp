<?php
// Set response header to JSON
header('Content-Type: application/json');
include_once 'func.php';
include 'cors.php';

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get the Authorization header
    $apiKey = validateApiKey($conn);
    $organization_id = getOrgIDbyApiKey($conn, $apiKey); // Get organization ID using API key

    // Get the data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if menu_ids are provided
    if (empty($data['menu_ids']) || empty($organization_id)) {
        echo json_encode(['status' => false, 'message' => 'Missing menu_ids or organization_id']);
        exit;
    }

    $menu_ids = $data['menu_ids'];

    // Loop through menu_ids and delete them
    foreach ($menu_ids as $menu_id) {
        // Check if the menu item belongs to the user's organization
        $stmt = $conn->prepare("SELECT organization_id FROM menu WHERE menu_id = ? LIMIT 1");
        $stmt->bind_param('s', $menu_id);
        $stmt->execute();
        $stmt->bind_result($menuOrgID);
        $stmt->fetch();
        $stmt->close();

        if ($menuOrgID !== $organization_id) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized operation for menu_id: ' . $menu_id]);
            exit;
        }

        // Delete associated images from the database and server
        $imageStmt = $conn->prepare("SELECT image_url FROM menu_images WHERE menu_id = ?");
        $imageStmt->bind_param('s', $menu_id);
        $imageStmt->execute();
        $result = $imageStmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $imagePath = $row['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the file from the server
            }
        }

        $imageStmt->close();

        // Delete the menu item
        $stmt = $conn->prepare("DELETE FROM menu WHERE menu_id = ? AND organization_id = ?");
        $stmt->bind_param('si', $menu_id, $organization_id);

        if (!$stmt->execute()) {
            echo json_encode(['status' => false, 'message' => 'Failed to delete menu_id: ' . $menu_id]);
            exit;
        }

        $stmt->close();
    }

    echo json_encode(['status' => true, 'message' => 'Menus deleted successfully']);
    $conn->close();
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
}
?>
