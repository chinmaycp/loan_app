# Simple Loan Application Logger & Eligibility API

## Description

This is a simple demonstration project simulating the first step of a loan application process. It includes a PHP web form to capture basic application data, stores it in an SQL database, and calls a Python (Flask/FastAPI) REST API to get a basic eligibility hint. The project showcases the integration of PHP, Python, SQL, and REST APIs, along with basic coding, debugging, and version control practices[cite: 1, 2]. Built for a junior developer role demonstration.

## Technologies Used

-   PHP (for web form and backend logic) [cite: 3, 9]
-   Python (for the eligibility hint API) [cite: 16]
-   Flask (or FastAPI - specify which one you used) [cite: 16]
-   SQL (MySQL/MariaDB or SQLite - specify which one) [cite: 6]
-   HTML [cite: 3]
-   cURL (PHP extension for API calls) [cite: 15]
-   Git / GitHub (for version control) [cite: 29]

## Setup Instructions

### Prerequisites

-   Git
-   PHP >= 7.x (with cURL extension enabled)
-   Web Server (Apache or Nginx - usually comes with XAMPP/MAMP)
-   MySQL/MariaDB Server (usually comes with XAMPP/MAMP) OR SQLite
-   Python >= 3.7
-   pip (Python package installer)

### Installation

1.  **Clone the repository:**
    ```bash
    git clone <your-repository-url.git>
    cd loan_project
    ```
2.  **Database Setup:**
    -   **(MySQL/MariaDB)** Create a database named `loan_app_db`.
    -   **(MySQL/MariaDB)** Import the schema: `mysql -u your_username -p loan_app_db < sql/schema.sql` (or use a tool like phpMyAdmin to run the `sql/schema.sql` script)[cite: 7].
    -   **(SQLite - If Used)** The database file will be created automatically.
3.  **PHP Configuration (if needed):**
    -   Ensure your web server (Apache via XAMPP/MAMP) is configured to serve files from the project directory.
    -   Update database credentials in `submit_application.php` if you are not using default XAMPP/MAMP settings (user: 'root', password: '').
4.  **Python API Setup:**
    ```bash
    cd python_api
    pip install -r requirements.txt [cite: 35]
    cd ..
    ```

## Running the Application

1.  **Start Database & Web Server:** Use XAMPP/MAMP control panel to start Apache and MySQL.
2.  **Start Python API:** Open a **separate terminal** window, navigate to the `python_api` directory, and run:
    ```bash
    python app.py
    ```
    Keep this terminal open. You should see output indicating the server is running on port 5000.
3.  **Access the Web Form:** Open your web browser and navigate to the URL pointing to your project directory (e.g., `http://localhost/loan_project/` or `http://127.0.0.1/loan_project/`).

## How to Use

1.  Fill in the loan application form fields[cite: 4].
2.  Click "Submit Application".
3.  A confirmation message will appear, including the eligibility hint received from the Python API[cite: 14].
4.  The application details are logged in the `loan_applications` database table[cite: 11].
