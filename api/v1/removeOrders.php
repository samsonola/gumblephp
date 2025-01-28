<?php

include_once 'func.php';
include 'cors.php';

header("Content-Type: application/json");

// Validate API key
$apiKey = validateApiKey($conn);

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Decode the JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['orderID'])) {
        echo json_encode(["error" => "OrderID is required."]);
        exit;
    }

    $orderID = $input['orderID'];

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

    // Delete order items first
    $queryItems = "DELETE FROM order_item WHERE orderID = ?";
    $stmtItems = mysqli_prepare($conn, $queryItems);

    if (!$stmtItems) {
        echo json_encode(["error" => "Failed to prepare delete item query."]);
        exit;
    }

    mysqli_stmt_bind_param($stmtItems, "s", $orderID);
    mysqli_stmt_execute($stmtItems);

    // Delete order
    $query = "DELETE FROM orders WHERE orderID = ? AND organization_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        echo json_encode(["error" => "Failed to prepare delete query."]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ss", $orderID, $organization_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => true, "message" => "Order deleted successfully."]);
    } else {
        echo json_encode(["error" => "Failed to delete order."]);
    }
} else {
    echo json_encode(["error" => "Invalid request method."]);
}

mysqli_close($conn);
?>
