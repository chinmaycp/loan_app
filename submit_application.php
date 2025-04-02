<?php
session_start(); // Start session to store feedback message

// --- Database Configuration (Ideally move to a separate config file) ---
$db_host = 'localhost'; // Or your DB host (e.g., 127.0.0.1)
$db_name = 'loan_app_db';
$db_user = 'root'; // Your DB username (default for XAMPP)
$db_pass = '';   // Your DB password (default for XAMPP)

// --- Receive and Basic Validation ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $applicant_name = trim($_POST['applicant_name'] ?? '');
    $requested_amount = trim($_POST['requested_amount'] ?? '');
    $monthly_income = trim($_POST['monthly_income'] ?? '');
    $loan_purpose = trim($_POST['loan_purpose'] ?? '');

    $errors = [];
    if (empty($applicant_name)) $errors[] = "Applicant name is required.";
    if (!is_numeric($requested_amount) || $requested_amount <= 0) $errors[] = "Valid requested amount is required.";
    if (!is_numeric($monthly_income) || $monthly_income <= 0) $errors[] = "Valid monthly income is required.";
    if (empty($loan_purpose)) $errors[] = "Loan purpose is required.";

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

            $_SESSION['message'] = "Application submitted successfully! Status: Pending.";
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
        header("Location: index.php");
        exit();
    }
} else {
    // Redirect if accessed directly without POST
    header("Location: index.php");
    exit();
}
?>
