<?php
// With menu_id, untested yet
// Set response header to JSON
header('Content-Type: application/json');
include_once 'func.php';  // Include the database and helper functions
include 'cors.php';       // Include CORS handler for cross-origin requests

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the Authorization header
    $apiKey = validateApiKey($conn);  // Validate API Key and get the organization_id

    // Check if API key validation was successful
    if (!$apiKey) {
        echo json_encode(['status' => false, 'message' => 'Invalid API Key']);
        exit;
    }

    // Get the organization_id using the API key
    $organization_id = getOrgIDbyApiKey($conn, $apiKey);

    if (!$organization_id) {
        echo json_encode(['status' => false, 'message' => 'Organization not found']);
        exit;
    }

    // Check if menu_id is provided in the request
    $menu_id = isset($_GET['menu_id']) ? $_GET['menu_id'] : null;

    // If menu_id is provided, query for a specific menu
    if ($menu_id) {
        $menusQuery = "SELECT * FROM menu WHERE organization_id = ? AND menu_id = ?";
        $stmt = $conn->prepare($menusQuery);
        $stmt->bind_param('ss', $organization_id, $menu_id);
    } else {
        // If no menu_id is provided, query for all menus
        $menusQuery = "SELECT * FROM menu WHERE organization_id = ?";
        $stmt = $conn->prepare($menusQuery);
        $stmt->bind_param('s', $organization_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any menus were found
    if ($result->num_rows > 0) {
        $menus = [];
        
        // Fetch menus data
        while ($menu = $result->fetch_assoc()) {
            $menuId = $menu['menu_id'];

            // Fetch associated images for the menu
            $imagesQuery = "SELECT image_url FROM menu_images WHERE menu_id = ?";
            $imageStmt = $conn->prepare($imagesQuery);
            $imageStmt->bind_param('s', $menuId);
            $imageStmt->execute();
            $imagesResult = $imageStmt->get_result();

            // Collect images for this menu
            $images = [];
            while ($image = $imagesResult->fetch_assoc()) {
                $images[] = $image['image_url'];
            }

            // Add menu with its images to the response data
            $menus[] = [
                'menu_id' => $menu['menu_id'],
                'names' => $menu['names'],
                'descriptions' => $menu['descriptions'],
                'price' => $menu['price'],
                'added_by' => $menu['added_by'],
                'category_id' => $menu['category_id'],
                'sub_category_id' => $menu['sub_category_id'],
                'discount_price' => $menu['discount_price'],
                'is_available' => $menu['is_available'],
                'images' => $images
            ];
        }

        // Close the statements
        $stmt->close();
        $imageStmt->close();

        // Return the response with menus and associated images
        echo json_encode(['status' => true, 'menus' => $menus]);
    } else {
        echo json_encode(['status' => false, 'message' => 'No menu(s) found for this user']);
    }

    // Close the database connection
    $conn->close();
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method. Only GET is allowed.']);
}




















// // Working without menu ID
// // Set response header to JSON
// header('Content-Type: application/json');
// include_once 'func.php';  // Include the database and helper functions
// include 'cors.php';       // Include CORS handler for cross-origin requests

// // Check if the request method is GET
// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
//     // Get the Authorization header
//     $apiKey = validateApiKey($conn);  // Validate API Key and get the organization_id

//     // Check if API key validation was successful
//     if (!$apiKey) {
//         echo json_encode(['status' => false, 'message' => 'Invalid API Key']);
//         exit;
//     }

//     // Get the organization_id using the API key
//     $organization_id = getOrgIDbyApiKey($conn, $apiKey);

//     if (!$organization_id) {
//         echo json_encode(['status' => false, 'message' => 'Organization not found']);
//         exit;
//     }

//     // Fetch menus for the given organization_id
//     $menusQuery = "SELECT * FROM menu WHERE organization_id = ?";
//     $stmt = $conn->prepare($menusQuery);
//     $stmt->bind_param('s', $organization_id);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     // Check if any menus were found
//     if ($result->num_rows > 0) {
//         $menus = [];
        
//         // Fetch menus data
//         while ($menu = $result->fetch_assoc()) {
//             $menuId = $menu['menu_id'];

//             // Fetch associated images for the menu
//             $imagesQuery = "SELECT image_url FROM menu_images WHERE menu_id = ?";
//             $imageStmt = $conn->prepare($imagesQuery);
//             $imageStmt->bind_param('s', $menuId);
//             $imageStmt->execute();
//             $imagesResult = $imageStmt->get_result();

//             // Collect images for this menu
//             $images = [];
//             while ($image = $imagesResult->fetch_assoc()) {
//                 $images[] = $image['image_url'];
//             }

//             // Add menu with its images to the response data
//             $menus[] = [
//                 'menu_id' => $menu['menu_id'],
//                 'names' => $menu['names'],
//                 'descriptions' => $menu['descriptions'],
//                 'price' => $menu['price'],
//                 'added_by' => $menu['added_by'],
//                 'category_id' => $menu['category_id'],
//                 'sub_category_id' => $menu['sub_category_id'],
//                 'discount_price' => $menu['discount_price'],
//                 'is_available' => $menu['is_available'],
//                 'images' => $images
//             ];
//         }

//         // Close the statements
//         $stmt->close();
//         $imageStmt->close();

//         // Return the response with menus and associated images
//         echo json_encode(['status' => true, 'menus' => $menus]);
//     } else {
//         echo json_encode(['status' => false, 'message' => 'No menu(s) found for this user']);
//     }

//     // Close the database connection
//     $conn->close();
// } else {
//     echo json_encode(['status' => false, 'message' => 'Invalid request method. Only GET is allowed.']);
// }


?>
