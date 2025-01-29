<?php
$jwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjo0LCJmaXJzdG5hbWUiOiJNYXZpbiIsImxhc3RuYW1lIjoiWml2YSIsImVtYWlsIjoicmVhbGRldm9sYXR1bmRlMTYzQGdtYWlsLmNvbSIsImlhdCI6MTczODEzODM0MSwiZXhwIjoxNzM4MTQxOTQxfQ.jAiJl3o60rNSvee0n_xu0qt-3TYPqQ9UiqhtbhP5o6Y"; // Replace with the actual token

$url = "http://localhost/gumblephp/api/v1/SignInGetData.php"; // Adjust the URL

$headers = [
    "Authorization: Bearer $jwt",
    "Content-Type: application/json"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    echo "Response: " . $response;
} else {
    echo "Error: " . $response;
}
?>
