<?php

include_once 'func.php';
include 'cors.php';

header("Content-Type: application/json");

// Validate API key
$apiKey = validateApiKey($conn);

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Decode the JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['orderID']) || !isset($input['order'])) {
        echo json_encode(["error" => "Invalid request data."]);
        exit;
    }

    $orderID = $input['orderID'];
    $order = $input['order'];

    // Check organization ownership
    $organization_id = getOrgIDbyApiKey($conn, $apiKey);
    $query = "SELECT * FROM orders WHERE orderID = ? AND organization_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        echo json_encode(["error" => "Failed to prepare query."]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ss", $orderID, $organization_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!mysqli_fetch_assoc($result)) {
        echo json_encode(["error" => "Order not found."]);
        exit;
    }

    // Update order fields
    $query = "UPDATE orders SET last_name = ?, first_name = ?, date = ?, table_number = ?, executed_by = ? WHERE orderID = ? AND organization_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        echo json_encode(["error" => "Failed to prepare update query."]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "sssssss", $order['last_name'], $order['first_name'], $order['date'], $order['table_number'], $order['executed_by'], $orderID, $organization_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => true, "message" => "Order updated successfully."]);
    } else {
        echo json_encode(["error" => "Failed to update order."]);
    }
} else {
    echo json_encode(["error" => "Invalid request method."]);
}

mysqli_close($conn);
?>
