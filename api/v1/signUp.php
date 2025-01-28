<?php
include_once 'func.php';
include 'cors.php';

if (!$conn) {
    die(json_encode(["error" => "Failed to connect to the resource."]));
}

// Set content type to JSON
header("Content-Type: application/json");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    // Validate required fields
    $requiredFields = ['firstname', 'lastname', 'phone', 'email'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            echo json_encode(["error" => "Missing required field: $field."]);
            exit;
        }
    }

    // Extract user data
    $firstname = mysqli_real_escape_string($conn, $input['firstname']);
    $lastname = mysqli_real_escape_string($conn, $input['lastname']);
    $phone = mysqli_real_escape_string($conn, $input['phone']);
    $email = mysqli_real_escape_string($conn, $input['email']);
    $organization_id = mysqli_real_escape_string($conn, generateRandomString("Gumb_", 15));

    // Check if the email already exists
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $checkStmt = mysqli_prepare($conn, $checkEmailQuery);

    if (!$checkStmt) {
        echo json_encode(["error" => "Failed to prepare SQL query for email check."]);
        exit;
    }

    // Bind the email parameter and execute the query
    mysqli_stmt_bind_param($checkStmt, "s", $email);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        // Email already exists
        echo json_encode(["error" => "Email already exists."]);
        mysqli_stmt_close($checkStmt);
        exit;
    }
    mysqli_stmt_close($checkStmt);

    // Generate a password
    $password = generateRandomString("pass",8); // 12-character password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Generate a unique API key
    $api_key = generateRandomString("PK_", 30); // 30-character unique key

    // Insert the user into the `users` table, including hashed password
    $insertQuery = "INSERT INTO users (firstname, lastname, phone, email, organization_id, api_key, auth_access) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = mysqli_prepare($conn, $insertQuery);

    if (!$insertStmt) {
        echo json_encode(["error" => "Failed to prepare SQL query for insertion."]);
        exit;
    }

    // Bind parameters to the query
    mysqli_stmt_bind_param($insertStmt, "sssssss", $firstname, $lastname, $phone, $email, $organization_id, $api_key, $hashedPassword);

    // Execute the query
    if (mysqli_stmt_execute($insertStmt)) {
        // Send welcome email
        $subject = "Welcome to Our Service!";
        $message = "Hello $firstname $lastname,\n\nWelcome to our service! Here are your account details:\n\n";
        $message .= "Name: $firstname $lastname\nPhone: $phone\nEmail: $email\nAPI Key: $api_key\nPassword: $password\n\n";
        $message .= "Please keep your credentials safe. All our API endpoints require an API key, kindly keep this handy for your developers\n\nThank you for signing up!";
        sendWelcomeEmail($email, $subject, $message); // Use sendWelcomeEmail function

        echo json_encode([
            "success" => true,
            "message" => "User signed up successfully.",
            "api_key" => $api_key
        ]);
    } else {
        echo json_encode(["error" => "Failed to sign up user."]);
    }

    mysqli_stmt_close($insertStmt);
} else {
    echo json_encode(["error" => "Invalid request method."]);
}

// Close the connection
mysqli_close($conn);
























// include_once 'func.php';
// include 'cors.php';

// if (!$conn) {
//     die(json_encode(["error" => "Failed to connect to the database."]));
// }

// // Set content type to JSON
// header("Content-Type: application/json");

// // Check if the request method is POST
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Decode the JSON input
//     $input = json_decode(file_get_contents("php://input"), true);

//     // Validate required fields
//     $requiredFields = ['firstname', 'lastname', 'phone', 'email'];
//     foreach ($requiredFields as $field) {
//         if (empty($input[$field])) {
//             echo json_encode(["error" => "Missing required field: $field."]);
//             exit;
//         }
//     }

//     // Extract user data
//     $firstname = mysqli_real_escape_string($conn, $input['firstname']);
//     $lastname = mysqli_real_escape_string($conn, $input['lastname']);
//     $phone = mysqli_real_escape_string($conn, $input['phone']);
//     $email = mysqli_real_escape_string($conn, $input['email']);
//     $organization_id = mysqli_real_escape_string($conn, generateRandomString("Gumb_", 15));

//     // Check if the email already exists
//     $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
//     $checkStmt = mysqli_prepare($conn, $checkEmailQuery);

//     if (!$checkStmt) {
//         echo json_encode(["error" => "Failed to prepare SQL query for email check."]);
//         exit;
//     }

//     // Bind the email parameter and execute the query
//     mysqli_stmt_bind_param($checkStmt, "s", $email);
//     mysqli_stmt_execute($checkStmt);
//     mysqli_stmt_store_result($checkStmt);

//     if (mysqli_stmt_num_rows($checkStmt) > 0) {
//         // Email already exists
//         echo json_encode(["error" => "Email already exists."]);
//         mysqli_stmt_close($checkStmt);
//         exit;
//     }
//     mysqli_stmt_close($checkStmt);

//     // Generate a unique API key
//     $api_key = generateRandomString("PK_", 30); // 30-character unique key

//     // Insert the user into the `users` table
//     $insertQuery = "INSERT INTO users (firstname, lastname, phone, email, organization_id, api_key) 
//                     VALUES (?, ?, ?, ?, ?, ?)";
//     $insertStmt = mysqli_prepare($conn, $insertQuery);

//     if (!$insertStmt) {
//         echo json_encode(["error" => "Failed to prepare SQL query for insertion."]);
//         exit;
//     }

//     // Bind parameters to the query
//     mysqli_stmt_bind_param($insertStmt, "ssssss", $firstname, $lastname, $phone, $email, $organization_id, $api_key);

//     // Execute the query
//     if (mysqli_stmt_execute($insertStmt)) {
//         echo json_encode([
//             "success" => true,
//             "message" => "User signed up successfully.",
//             "api_key" => $api_key
//         ]);
//     } else {
//         echo json_encode(["error" => "Failed to sign up user."]);
//     }

//     mysqli_stmt_close($insertStmt);
// } else {
//     echo json_encode(["error" => "Invalid request method."]);
// }

// // Close the connection
// mysqli_close($conn);






















