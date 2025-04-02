import pandas as pd
import mysql.connector # Or your chosen SQL connector
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LogisticRegression
# from sklearn.tree import DecisionTreeClassifier # Alternative model
from sklearn.metrics import accuracy_score
from sklearn.preprocessing import LabelEncoder

print("--- Starting ML Model Training Script ---")

# --- Database Configuration ---
# Use the same credentials as your PHP script
# Consider loading from a config file or environment variables in a real project
DB_CONFIG = {
    'host': 'localhost', # Or 127.0.0.1
    'user': 'root',      # Your DB username
    'password': '',      # Your DB password
    'database': 'loan_app_db'
}

try:
    # --- Load Data ---
    print("Connecting to database...")
    conn = mysql.connector.connect(**DB_CONFIG)
    # Select only applications where a decision was made by the simple API
    query = "SELECT requested_amount, monthly_income, eligibility_status FROM loan_applications WHERE eligibility_status IN ('Possible', 'Review Needed')"
    df = pd.read_sql(query, conn)
    conn.close()
    print(f"Loaded {len(df)} relevant records from the database.")

    if len(df) < 10: # Need minimum data to train/test split
         print("Not enough data to train a model (need at least 10 records with 'Possible' or 'Review Needed' status).")
         exit()

    # --- Data Preparation ---
    print("Preparing data...")
    # Features (Input)
    X = df[['requested_amount', 'monthly_income']]

    # Target (Output) - Convert 'Possible'/'Review Needed' to 1/0
    le = LabelEncoder()
    y = le.fit_transform(df['eligibility_status']) # 'Possible' might become 1, 'Review Needed' 0
    print("Target mapping:", dict(zip(le.classes_, le.transform(le.classes_))))


    # Split data into training and testing sets (e.g., 80% train, 20% test)
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42, stratify=y)
    print(f"Training set size: {len(X_train)}, Test set size: {len(X_test)}")

    # --- Train Model ---
    print("Training Logistic Regression model...")
    # model = DecisionTreeClassifier(random_state=42) # Alternative
    model = LogisticRegression(random_state=42)
    model.fit(X_train, y_train)

    # --- Evaluate Model ---
    print("Evaluating model...")
    y_pred = model.predict(X_test)
    accuracy = accuracy_score(y_test, y_pred)
    print(f"Model Accuracy on Test Set: {accuracy:.4f}")

    # Optional: Show feature importance or coefficients for explanation
    if hasattr(model, 'coef_'):
         print("Model Coefficients:", model.coef_)
    # elif hasattr(model, 'feature_importances_'):
    #      print("Feature Importances:", model.feature_importances_)


except mysql.connector.Error as err:
    print(f"Database Error: {err}")
except Exception as e:
    print(f"An error occurred: {e}")

print("--- ML Model Training Script Finished ---")
