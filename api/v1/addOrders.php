<?php

include_once 'func.php';
include 'cors.php';

// Set content type to JSON
header("Content-Type: application/json");

// Validate API key
$apiKey = validateApiKey($conn);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['order']) || !isset($input['order_items']) || !is_array($input['order_items'])) {
        echo json_encode(["error" => "Invalid request data."]);
        exit;
    }

    // Extract order data
    $order = $input['order'];
    $orderItems = $input['order_items'];

    // Validate order fields
    $requiredFields = ['last_name', 'first_name', 'date', 'table_number', 'executed_by'];
    foreach ($requiredFields as $field) {
        if (empty($order[$field])) {
            echo json_encode(["error" => "Missing required order field: $field."]);
            exit;
        }
    }

    // Insert order into the `orders` table
    $organization_id = getOrgIDbyApiKey($conn, $apiKey);

    // Prepare query for inserting order
    $query = "INSERT INTO orders (last_name, first_name, orderID, date, table_number, executed_by, organization_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        echo json_encode(["error" => "Failed to prepare order query."]);
        exit;
    }

    // Generate orderID once and bind parameters for the order
    $orderID = generateRandomString("Ord_id", 20);  // Only generate once
    mysqli_stmt_bind_param($stmt, "sssssss", $order['last_name'], $order['first_name'], $orderID, $order['date'], $order['table_number'], $order['executed_by'], $organization_id);

    // Execute the order insertion
    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(["error" => "Failed to insert order."]);
        exit;
    }

    // Use the generated orderID as the primary ID for consistency
    $orderPrimaryID = $orderID;

    // Insert order items into the `order_item` table
    $query = "INSERT INTO order_item (menu_name, orderID, price, status) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        echo json_encode(["error" => "Failed to prepare order item query."]);
        exit;
    }

    foreach ($orderItems as $item) {
        if (!isset($item['menu_name'], $item['price'], $item['status'])) {
            echo json_encode(["error" => "Invalid order item data."]);
            exit;
        }

        // Bind the actual orderID (the same one used in the orders table) to the order items
        mysqli_stmt_bind_param($stmt, "ssss", $item['menu_name'], $orderPrimaryID, $item['price'], $item['status']);

        // Execute the order item insertion
        if (!mysqli_stmt_execute($stmt)) {
            echo json_encode(["error" => "Failed to insert order item."]);
            exit;
        }
    }

    // Return success message with the orderID
    echo json_encode([
        "status" => true,
        "message" => "Order and order items inserted successfully.",
        "orderID" => $orderPrimaryID
    ]);
} else {
    echo json_encode(["error" => "Invalid request method."]);
}

mysqli_close($conn);
?>
