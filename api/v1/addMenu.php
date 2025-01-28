<?php
// Set response header to JSON
header('Content-Type: application/json');
include_once 'func.php';
include 'cors.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the Authorization header
    $apiKey = validateApiKey($conn);
    $organization_id = getOrgIDbyApiKey($conn, $apiKey); // Function to get organization ID using API key

    // Check if all required parameters are present
    $requiredParams = ['names', 'price', 'added_by', 'category_id'];
    foreach ($requiredParams as $param) {
        if (empty($_POST[$param]) || empty($organization_id)) {
            echo json_encode(['status' => false, 'message' => "Missing parameter: $param"]);
            exit;
        }
    }

    // Sanitize and assign POST data
    $names = $_POST['names'];
    $price = $_POST['price'];
    $added_by = $_POST['added_by'];
    $menu_id = generateRandomString("Menid_", 15); // Custom generated menu ID
    $descriptions = $_POST['descriptions'] ?? null;
    $category_id = $_POST['category_id'] ?? null;
    $sub_category_id = $_POST['sub_category_id'] ?? null;
    $discount_price = $_POST['discount_price'] ?? null;
    $is_available = isset($_POST['is_available']) ? (int)$_POST['is_available'] : null;
    $is_multi_option = isset($_POST['is_multi_option']) ? (int)$_POST['is_multi_option'] : null;

    // Prepare SQL query to insert menu data
    $stmt = $conn->prepare("INSERT INTO menu (names, descriptions, price, added_by, organization_id, menu_id, category_id, sub_category_id, discount_price, is_available, is_multi_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        'ssdsssssiii',
        $names,
        $descriptions,
        $price,
        $added_by,
        $organization_id,
        $menu_id,
        $category_id,
        $sub_category_id,
        $discount_price,
        $is_available,
        $is_multi_option
    );

    if ($stmt->execute()) {
        $stmt->close(); // Close the statement after executing

        // Handle image uploads if provided
        if (!empty($_FILES['images']['name'][0])) {
            $imageUrls = [];
            $uploadDir = 'images/';

            // Ensure the upload directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                $fileName = uniqid() . "_" . basename($_FILES['images']['name'][$index]); // Unique filename
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($tmpName, $filePath)) {
                    $imageUrls[] = $filePath;

                    // Insert image URL into the database, referencing the generated menu_id
                    $imageStmt = $conn->prepare("INSERT INTO menu_images (menu_id, image_url) VALUES (?, ?)");
                    $imageStmt->bind_param('ss', $menu_id, $filePath); // Use the generated menu_id
                    $imageStmt->execute();
                    $imageStmt->close();
                } else {
                    echo json_encode(['status' => false, 'message' => "Failed to upload image: $fileName"]);
                    exit;
                }
            }
        }

        echo json_encode(['status' => true, 'message' => 'Menu has been added successfully']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to add menu']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
}




















// // Set response header to JSON
// header('Content-Type: application/json');
// include_once 'func.php';
// include 'cors.php';

// // Check if the request method is POST
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Get the Authorization header
//     $apiKey = validateApiKey($conn);
//     $organization_id = getOrgIDbyApiKey($conn, $apiKey); // Function to get organization ID using API key

//     // Check if all required parameters are present
//     $requiredParams = ['names', 'price', 'added_by', 'category_id'];
//     foreach ($requiredParams as $param) {
//         if (empty($_POST[$param]) || empty($organization_id)) {
//             echo json_encode(['status' => false, 'message' => "Missing parameter: $param"]);
//             exit;
//         }
//     }

//     // Sanitize and assign POST data
//     $names = $_POST['names'];
//     $price = $_POST['price'];
//     $added_by = $_POST['added_by'];
//     $menu_id = generateRandomString("Menid_", 15);
//     $descriptions = $_POST['descriptions'] ?? null;
//     $category_id = $_POST['category_id'] ?? null;
//     $sub_category_id = $_POST['sub_category_id'] ?? null;
//     $discount_price = $_POST['discount_price'] ?? null;
//     $is_available = isset($_POST['is_available']) ? (int)$_POST['is_available'] : null;
//     $is_multi_option = isset($_POST['is_multi_option']) ? (int)$_POST['is_multi_option'] : null;
//     // 'before ins: '.var_dump($organization_id);
//     // Prepare SQL query to insert menu data
//     $stmt = $conn->prepare("INSERT INTO menu (names, descriptions, price, added_by, organization_id, menu_id, category_id, sub_category_id, discount_price, is_available, is_multi_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
//     $stmt->bind_param(
//         'ssdsssssiii',
//         $names,
//         $descriptions,
//         $price,
//         $added_by,
//         $organization_id,
//         $menu_id,
//         $category_id,
//         $sub_category_id,
//         $discount_price,
//         $is_available,
//         $is_multi_option
//     );

//     if ($stmt->execute()) {
//         // 'after ins: '.var_dump($organization_id);
//         $lastMenuId = $stmt->insert_id; // Get the last inserted ID
//         $stmt->close();

//         // Handle image uploads if provided
//         if (!empty($_FILES['images']['name'][0])) {
//             $imageUrls = [];
//             $uploadDir = 'images/';

//             // Ensure the upload directory exists
//             if (!is_dir($uploadDir)) {
//                 mkdir($uploadDir, 0777, true);
//             }

//             foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
//                 $fileName = uniqid() . "_" . basename($_FILES['images']['name'][$index]); // Unique filename
//                 $filePath = $uploadDir . $fileName;

//                 if (move_uploaded_file($tmpName, $filePath)) {
//                     $imageUrls[] = $filePath;

//                     // Insert image URL into the database
//                     $imageStmt = $conn->prepare("INSERT INTO menu_images (menu_id, image_url) VALUES (?, ?)");
//                     $imageStmt->bind_param('is', $lastMenuId, $filePath);
//                     $imageStmt->execute();
//                     $imageStmt->close();
//                 } else {
//                     echo json_encode(['status' => false, 'message' => "Failed to upload image: $fileName"]);
//                     exit;
//                 }
//             }
//         }

//         echo json_encode(['status' => true, 'message' => 'Menu has been added successfully']);
//     } else {
//         echo json_encode(['status' => false, 'message' => 'Failed to add menu']);
//     }

//     $conn->close();
// } else {
//     echo json_encode(['status' => false, 'message' => 'Invalid request method']);
// }
















// // Set response header to JSON
// header('Content-Type: application/json');
// include_once 'func.php';
// include 'cors.php';

// // Check if the request method is POST
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Get the Authorization header
//     $apiKey = validateApiKey($conn);

//     // Check if all required parameters are present
//     $requiredParams = ['names', 'price', 'added_by', 'category_id'];
//     foreach ($requiredParams as $param) {
//         if (empty($_POST[$param])) {
//             echo json_encode(['status' => false, 'message' => "Missing parameter: $param"]);
//             exit;
//         }
//     }

//     // Sanitize and assign POST data
//     $names = $_POST['names'];
//     $price = $_POST['price'];
//     $added_by = $_POST['added_by'];
//     $organization_id = getOrgIDbyApiKey($conn, $apiKey); // Function to get organization ID using API key
//     $menu_id = generateRandomString("Menid_", 15);
//     $descriptions = $_POST['descriptions'] ?? null;
//     $category_id = $_POST['category_id'] ?? null;
//     $sub_category_id = $_POST['sub_category_id'] ?? null;
//     $discount_price = $_POST['discount_price'] ?? null;
//     $is_available = isset($_POST['is_available']) ? (int)$_POST['is_available'] : null;
//     $is_multi_option = isset($_POST['is_multi_option']) ? (int)$_POST['is_multi_option'] : null;

//     // Prepare SQL query to insert menu data
//     $stmt = $conn->prepare("INSERT INTO menu (names, descriptions, price, added_by, organization_id, menu_id, category_id, sub_category_id, discount_price, is_available, is_multi_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
//     $stmt->bind_param(
//         'ssdsisssiii',
//         $names,
//         $descriptions,
//         $price,
//         $added_by,
//         $organization_id,
//         $menu_id,
//         $category_id,
//         $sub_category_id,
//         $discount_price,
//         $is_available,
//         $is_multi_option
//     );

//     if ($stmt->execute()) {
//         $lastMenuId = $stmt->insert_id; // Get the last inserted ID
//         $stmt->close();

//         // Handle image uploads if provided
//         if (!empty($_FILES['images']['name'][0])) {
//             $imageUrls = [];
//             $uploadDir = 'images/';

//             // Ensure the upload directory exists
//             if (!is_dir($uploadDir)) {
//                 mkdir($uploadDir, 0777, true);
//             }

//             foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
//                 $fileName = uniqid() . "_" . basename($_FILES['images']['name'][$index]); // Unique filename
//                 $filePath = $uploadDir . $fileName;

//                 if (move_uploaded_file($tmpName, $filePath)) {
//                     $imageUrls[] = $filePath;

//                     // Insert image URL into the database
//                     $imageStmt = $conn->prepare("INSERT INTO menu_images (menu_id, image_url) VALUES (?, ?)");
//                     $imageStmt->bind_param('is', $lastMenuId, $filePath);
//                     $imageStmt->execute();
//                     $imageStmt->close();
//                 } else {
//                     echo json_encode(['status' => false, 'message' => "Failed to upload image: $fileName"]);
//                     exit;
//                 }
//             }
//         }

//         echo json_encode(['status' => true, 'message' => 'Menu has been added successfully']);
//     } else {
//         echo json_encode(['status' => false, 'message' => 'Failed to add menu']);
//     }

//     $conn->close();
// } else {
//     echo json_encode(['status' => false, 'message' => 'Invalid request method']);
// }

// // Updated checkApiKey functio