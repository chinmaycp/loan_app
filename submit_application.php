<?php
require_once 'config.php';

session_start(); // Start session to store feedback message

// --- Database Configuration (import from Config file) ---
$db_host = DB_HOST;
$db_name = DB_NAME;
$db_user = DB_USER;
$db_pass = DB_PASS;
$api_key = PYTHON_API_KEY;
$api_url = PYTHON_API_URL;

// --- Receive and Basic Validation ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $applicant_name = filter_input(INPUT_POST, 'applicant_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $requested_amount = filter_input(INPUT_POST, 'requested_amount', FILTER_VALIDATE_FLOAT);
    $monthly_income = filter_input(INPUT_POST, 'monthly_income', FILTER_VALIDATE_FLOAT);
    $loan_purpose = filter_input(INPUT_POST, 'loan_purpose', FILTER_SANITIZE_SPECIAL_CHARS);

    $errors = [];
    if (empty(trim($applicant_name))) $errors[] = "Applicant name is required.";
    if (!is_numeric($requested_amount) || $requested_amount === false || $requested_amount <= 0) $errors[] = "Valid requested amount (positive number) is required.";
    if (!is_numeric($monthly_income) || $monthly_income === false || $monthly_income <= 0) $errors[] = "Valid monthly income (non-negative number) is required.";
    if (empty(trim($loan_purpose))) $errors[] = "Loan purpose is required.";

    if (empty($errors)) {
        // --- Database Connection & Insertion ---
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO loan_applications (applicant_name, requested_amount, monthly_income, loan_purpose, eligibility_status) VALUES (:name, :amount, :income, :purpose, 'Pending')";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':name', $applicant_name);
            $stmt->bindParam(':amount', $requested_amount);
            $stmt->bindParam(':income', $monthly_income);
            $stmt->bindParam(':purpose', $loan_purpose);

            $stmt->execute();

            $last_id = $pdo->lastInsertId(); // Get the ID of the inserted record

            // --- Prepare Data for Python API ---
            $api_data = [
                'amount' => $requested_amount, // Make sure this is the numeric value
                'income' => $monthly_income    // Make sure this is the numeric value
            ];
            $api_payload = json_encode($api_data);

            // --- Call Python API using cURL ---
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $api_payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($api_payload)
            ]);
            // Add the API Key Header to cURL options
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($api_payload),
                'X-API-Key: ' . $api_key // Add this line
            ]);

            $api_response_body = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            $eligibility_status = 'Pending (API Error)'; // Default status if API call fails

            if ($curl_error) {
                // Log cURL error (replace with proper logging)
                error_log("cURL Error calling API: " . $curl_error);
            } elseif ($http_code == 200) {
                $api_response_data = json_decode($api_response_body, true); // Decode JSON as associative array
                if ($api_response_data && isset($api_response_data['status'])) {
                    $eligibility_status = $api_response_data['status']; // Get status from API

                    // --- Update Database with API Status ---
                    $update_sql = "UPDATE loan_applications SET eligibility_status = :status WHERE id = :id";
                    $update_stmt = $pdo->prepare($update_sql);
                    $update_stmt->bindParam(':status', $eligibility_status);
                    $update_stmt->bindParam(':id', $last_id, PDO::PARAM_INT);
                    $update_stmt->execute();

                } else {
                    // Log invalid API response (replace with proper logging)
                    error_log("Invalid JSON response from API: " . $api_response_body);
                    $eligibility_status = 'Pending (API Response Error)';
                }
            } else {
                // Log HTTP error (replace with proper logging)
                error_log("API returned HTTP status code: " . $http_code . " Body: " . $api_response_body);
                $eligibility_status = 'Pending (API HTTP Error)';
            }

            $_SESSION['message'] = "Application submitted successfully! ID: " . $last_id . ". Eligibility Hint: " . htmlspecialchars($eligibility_status);
            header("Location: index.php"); // Redirect back to form
            exit();

        } catch (PDOException $e) {
            // Basic error handling (log properly in a real app)
            $_SESSION['message'] = "Error submitting application: " . $e->getMessage();
            header("Location: index.php"); // Redirect back
            exit();
        }
    } else {
         // Store errors and redirect back
        $_SESSION['message'] = "Submission failed: " . implode(" ", $errors);
        $_SESSION['form_data'] = $_POST; // Store form data to repopulate
        header("Location: index.php");
        exit();
    }
} else {
    // Redirect if accessed directly without POST
    header("Location: index.php");
    exit();
}
?>
