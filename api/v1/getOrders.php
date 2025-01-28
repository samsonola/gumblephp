<?php

include_once 'func.php';
include 'cors.php';

header("Content-Type: application/json");

// Validate API key
$apiKey = validateApiKey($conn);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get orderID from query parameters
    $orderID = isset($_GET['orderID']) ? $_GET['orderID'] : null;

    if (!$orderID) {
        echo json_encode(["error" => "OrderID is required."]);
        exit;
    }

    // Fetch order details
    $query = "SELECT * FROM orders WHERE orderID = ? AND organization_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    $organization_id = getOrgIDbyApiKey($conn, $apiKey);

    if (!$stmt) {
        echo json_encode(["error" => "Failed to prepare query."]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ss", $orderID, $organization_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($order = mysqli_fetch_assoc($result)) {
        // Fetch order items
        $queryItems = "SELECT * FROM order_item WHERE orderID = ?";
        $stmtItems = mysqli_prepare($conn, $queryItems);

        if (!$stmtItems) {
            echo json_encode(["error" => "Failed to prepare item query."]);
            exit;
        }

        mysqli_stmt_bind_param($stmtItems, "s", $orderID);
        mysqli_stmt_execute($stmtItems);
        $resultItems = mysqli_stmt_get_result($stmtItems);

        $order['order_items'] = [];
        while ($item = mysqli_fetch_assoc($resultItems)) {
            $order['order_items'][] = $item;
        }

        echo json_encode($order);
    } else {
        echo json_encode(["error" => "Order not found."]);
    }
} else {
    echo json_encode(["error" => "Invalid request method."]);
}

mysqli_close($conn);
?>
