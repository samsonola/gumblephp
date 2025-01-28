<?php
// Update Menu Endpoint
header('Content-Type: application/json');
include_once 'func.php';
include 'cors.php';

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $apiKey = validateApiKey($conn);
    $organization_id = getOrgIDbyApiKey($conn, $apiKey);

    parse_str(file_get_contents("php://input"), $putVars); // Parse PUT data

    $requiredParams = ['menu_id', 'names', 'price', 'category_id'];
    foreach ($requiredParams as $param) {
        if (empty($putVars[$param]) || empty($organization_id)) {
            echo json_encode(['status' => false, 'message' => "Missing parameter: $param"]);
            exit;
        }
    }

    $menu_id = $putVars['menu_id'];
    $names = $putVars['names'];
    $price = $putVars['price'];
    $discount_price = $putVars['discount_price'] ?? null;
    $category_id = $putVars['category_id'];
    $sub_category_id = $putVars['sub_category_id'] ?? null;
    $is_available = isset($putVars['is_available']) ? (int)$putVars['is_available'] : null;

    $stmt = $conn->prepare("UPDATE menu SET names = ?, price = ?, discount_price = ?, category_id = ?, sub_category_id = ?, is_available = ? 
        WHERE menu_id = ? AND organization_id = ?");
    $stmt->bind_param(
        'sdsissss',
        $names,
        $price,
        $discount_price,
        $category_id,
        $sub_category_id,
        $is_available,
        $menu_id,
        $organization_id
    );

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['status' => true, 'message' => 'Menu updated successfully']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Update failed or no changes made']);
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
}
