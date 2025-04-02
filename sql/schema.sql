CREATE TABLE loan_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_name VARCHAR(255),
    requested_amount DECIMAL(10, 2),
    monthly_income DECIMAL(10, 2),
    loan_purpose VARCHAR(255),
    submission_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    eligibility_status VARCHAR(50) DEFAULT 'Pending'
);
