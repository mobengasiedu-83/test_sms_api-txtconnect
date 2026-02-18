<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $apiKey = $_POST['apiKey'];  // Fetch the API key from the form
    $to = $_POST['to'];
    $from = $_POST['from'];
    $unicode = $_POST['unicode'];
    $sms = $_POST['sms'];

    // API URL
    //$apiUrl = "https://txtconnect.net/dev/api/sms/send";
    $apiUrl = "https://api.txtconnect.net/dev/api/sms/send";

    // Initialize cURL session
    $ch = curl_init($apiUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'to' => $to,
        'from' => $from,
        'unicode' => $unicode,
        'sms' => $sms,
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-type: application/x-www-form-urlencoded",
        "Authorization: Bearer $apiKey",
    ]);

    // Execute cURL request
    $apiResponse = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        $logMessage = sprintf(
            "[%s] cURL Error: %s\n",
            date('Y-m-d H:i:s'),
            $error
        );
        file_put_contents('logs/sms_log.txt', $logMessage, FILE_APPEND);
        echo "cURL Error: $error";
    } else {
        // Get HTTP response code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Prepare log message
        $logMessage = sprintf(
            "[%s] Status Code: %d\nResponse: %s\n",
            date('Y-m-d H:i:s'),
            $httpCode,
            $apiResponse
        );
        file_put_contents('logs/sms_log.txt', $logMessage, FILE_APPEND);

        // Display response to the user
        echo "<html><body>";
        echo "<h1>Response logged. Redirecting back to the form in 5 seconds...</h1>";
        echo "<script type='text/javascript'>";
        echo "setTimeout(function() {";
        echo "    window.location.href = 'index.html';";
        echo "}, 5000);";  // 5000 milliseconds = 5 seconds
        echo "</script>";
        echo "</body></html>";
    }

    // Close cURL session
    curl_close($ch);
} else {
    echo "Invalid request method!";
}
