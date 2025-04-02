<!DOCTYPE html>
<html>
<head>
    <title>Simple Loan Application</title>
</head>
<body>
    <h1>Loan Application Form</h1>
    <form action="submit_application.php" method="POST">
        <div>
            <label for="applicant_name">Applicant Name:</label>
            <input type="text" id="applicant_name" name="applicant_name" required>
        </div>
        <div>
            <label for="requested_amount">Requested Loan Amount:</label>
            <input type="number" id="requested_amount" name="requested_amount" step="0.01" required>
        </div>
        <div>
            <label for="monthly_income">Monthly Income:</label>
            <input type="number" id="monthly_income" name="monthly_income" step="0.01" required>
        </div>
        <div>
            <label for="loan_purpose">Loan Purpose:</label>
            <input type="text" id="loan_purpose" name="loan_purpose" required>
        </div>
        <div>
            <button type="submit">Submit Application</button>
        </div>
    </form>
    <?php
        // Optional: Display feedback messages here if redirected back
        session_start();
        if (isset($_SESSION['message'])) {
            echo "<p>" . htmlspecialchars($_SESSION['message']) . "</p>";
            unset($_SESSION['message']); // Clear message after displaying
        }
    ?>
</body>
</html>
