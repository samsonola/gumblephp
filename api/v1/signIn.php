<?php
include_once 'func.php';
include 'cors.php';
use \Firebase\JWT\JWT;

if (!$conn) {
    die(json_encode(["error" => "Failed to connect to the database."]));
}

// Set content type to JSON
header("Content-Type: application/json");

// Secret key for JWT
$jwtSecretKey = "YOUR_SECRET_KEY"; // Change this to your actual secret key

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the API key from the request header
    $headers = apache_request_headers();
    if (isset($headers['API-Key'])) {
        $api_key = $headers['API-Key'];
    } else {
        echo json_encode(["error" => "API key is missing."]);
        exit;
    }

    // Decode the JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    // Validate required fields
    $requiredFields = ['email', 'password'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            echo json_encode(["error" => "Missing required field: $field."]);
            exit;
        }
    }

    // Extract user data
    $email = mysqli_real_escape_string($conn, $input['email']);
    $password = mysqli_real_escape_string($conn, $input['password']);

    // Check if the email exists
    $checkUserQuery = "SELECT id, password, api_key, firstname, lastname FROM users WHERE email = ?";
    $checkStmt = mysqli_prepare($conn, $checkUserQuery);

    if (!$checkStmt) {
        echo json_encode(["error" => "Failed to prepare SQL query."]);
        exit;
    }

    // Bind the email parameter and execute the query
    mysqli_stmt_bind_param($checkStmt, "s", $email);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    // Check if email exists
    if (mysqli_stmt_num_rows($checkStmt) == 0) {
        echo json_encode(["error" => "Invalid email."]);
        mysqli_stmt_close($checkStmt);
        exit;
    }

    mysqli_stmt_bind_result($checkStmt, $userId, $storedPassword, $storedApiKey, $firstname, $lastname);
    mysqli_stmt_fetch($checkStmt);
    mysqli_stmt_close($checkStmt);

    // Verify API key
    if ($api_key !== $storedApiKey) {
        echo json_encode(["error" => "Invalid API key."]);
        exit;
    }

    // Verify password
    if (!password_verify($password, $storedPassword)) {
        echo json_encode(["error" => "Invalid password."]);
        exit;
    }

    // Create JWT payload
    $payload = [
        "user_id" => $userId,
        "firstname" => $firstname,
        "lastname" => $lastname,
        "email" => $email,
        "iat" => time(),
        "exp" => time() + 3600 // Token expiration time (1 hour)
    ];

    // Encode the payload using the secret key
    $jwt = JWT::encode($payload, $jwtSecretKey);

    // Respond with the JWT token
    echo json_encode([
        "success" => true,
        "message" => "User signed in successfully.",
        "jwt" => $jwt
    ]);
} else {
    echo json_encode(["error" => "Invalid request method."]);
}

// Close the connection
mysqli_close($conn);
?>
