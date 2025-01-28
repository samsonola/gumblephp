<?php

include 'server.php';
function generateRandomString($prefix, $totalLength) {
    // Define the characters to use for the random part of the string
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';

    // Calculate the length of the random part
    $randomPartLength = $totalLength - strlen($prefix);

    // Ensure the total length is greater than the prefix length
    if ($randomPartLength <= 0) {
        return $prefix; // If not, return the prefix as is
    }

    // Generate the random part of the string
    for ($i = 0; $i < $randomPartLength; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    // Concatenate the prefix with the random string
    return $prefix . $randomString;
}

function checkApiKey($conn, $api_key) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE api_key = ?");
    $stmt->bind_param("s", $api_key);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc(); // Return user details if API key is valid
    } else {
        return false; // Return false if API key is invalid
    }
}

function getOrgIDbyApiKey($conn, $apiKey) {
    // Prepare the SQL query
    $query = "SELECT organization_id FROM users WHERE api_key = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        die(json_encode(["error" => "Failed to prepare the SQL query."]));
    }

    // Bind the API key parameter to the query
    mysqli_stmt_bind_param($stmt, "s", $apiKey);

    // Execute the query
    mysqli_stmt_execute($stmt);

    // Fetch the result
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['organization_id'];
    }

    // Return null if no matching organization_id is found
    return null;
}


function validateApiKey($conn) {
    // Get the Authorization header
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $apiKey = $matches[1];
            
            // Check API Key and end process if invalid
            $apiKeyDetails = checkApiKey($conn, $apiKey);
            if (!$apiKeyDetails) {
                echo json_encode(['status' => false, 'message' => 'Invalid API Key']);
                exit;
            }

            return $apiKey; // Return the API Key if valid
        } else {
            echo json_encode(['status' => false, 'message' => 'Invalid Authorization header format']);
            exit;
        }
    } else {
        echo json_encode(['status' => false, 'message' => 'Authorization header missing']);
        exit;
    }
}


// Function to send a welcome email
function sendWelcomeEmail($to, $subject, $message) {
    $headers = "From: no-reply@yourdomain.com" . "\r\n" .
               "Reply-To: no-reply@yourdomain.com" . "\r\n" .
               "Content-Type: text/plain; charset=UTF-8";
    mail($to, $subject, $message, $headers);
}


?>
