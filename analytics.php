<?php

// --- Database Configuration (import from Config file) ---
require_once 'config.php';
$db_host = DB_HOST;
$db_name = DB_NAME;
$db_user = DB_USER;
$db_pass = DB_PASS;
$api_key = PYTHON_API_KEY;
$api_url = PYTHON_API_URL;

$total_apps = 0;
$avg_amount = 0;
$status_counts = [];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query 1: Total applications
    $stmt_total = $pdo->query("SELECT COUNT(*) as total_apps FROM loan_applications");
    $result_total = $stmt_total->fetch(PDO::FETCH_ASSOC);
    $total_apps = $result_total['total_apps'] ?? 0;

    // Query 2: Average amount
    $stmt_avg = $pdo->query("SELECT AVG(requested_amount) as avg_amount FROM loan_applications");
    $result_avg = $stmt_avg->fetch(PDO::FETCH_ASSOC);
    $avg_amount = $result_avg['avg_amount'] ?? 0;
    // Format nicely
    $avg_amount = number_format((float)$avg_amount, 2);


    // Query 3: Counts by status
    $stmt_status = $pdo->query("SELECT eligibility_status, COUNT(*) as status_count FROM loan_applications GROUP BY eligibility_status");
    $status_counts = $stmt_status->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Basic error handling
    $error_message = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Loan Application Analytics</title>
</head>
<body>
    <h1>Basic Loan Application Stats</h1>

    <p><a href="index.php">Back to Application Form</a></p>

    <?php if (isset($error_message)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php else: ?>
        <p>Total Applications Logged: <strong><?php echo $total_apps; ?></strong></p>
        <p>Average Requested Amount: <strong>$<?php echo htmlspecialchars($avg_amount); ?></strong></p>

        <h2>Applications by Status:</h2>
        <?php if (!empty($status_counts)): ?>
            <ul>
                <?php foreach ($status_counts as $row): ?>
                    <li><?php echo htmlspecialchars($row['eligibility_status']); ?>: <strong><?php echo $row['status_count']; ?></strong></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No application statuses found.</p>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>
