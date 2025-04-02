from flask import Flask, request, jsonify
import logging # Optional: for basic logging

# Configure basic logging (optional but good practice)
logging.basicConfig(level=logging.INFO)

app = Flask(__name__)

@app.route('/check_eligibility', methods=['POST'])
def check_eligibility():
    """
    Checks basic loan eligibility based on amount and income.
    Expects JSON: {"amount": 10000, "income": 3500}
    Returns JSON: {"status": "Possible"} or {"status": "Review Needed"}
    """
    app.logger.info('Received request for /check_eligibility') # Log request
    try:
        data = request.get_json()
        app.logger.info(f'Request data: {data}') # Log received data

        if not data or 'amount' not in data or 'income' not in data:
            app.logger.warning('Missing amount or income in request data')
            return jsonify({"error": "Missing 'amount' or 'income' in JSON payload"}), 400

        try:
            requested_amount = float(data['amount'])
            monthly_income = float(data['income'])
        except ValueError:
            app.logger.warning('Invalid numeric value for amount or income')
            return jsonify({"error": "Invalid numeric value for 'amount' or 'income'"}), 400

        # --- Extremely Simple Eligibility Rule --- [cite: 18]
        # This is intentionally basic for demonstration purposes.
        if monthly_income > (requested_amount / 3):
            status = 'Possible'
        else:
            status = 'Review Needed'
        # --- End Rule ---

        app.logger.info(f'Calculated status: {status}') # Log result
        response = {"status": status} # [cite: 19]
        return jsonify(response) # [cite: 20]

    except Exception as e:
        app.logger.error(f'Error processing request: {e}') # Log unexpected errors
        return jsonify({"error": "An internal server error occurred"}), 500

if __name__ == '__main__':
    # Runs the app on http://127.0.0.1:5000 by default
    # Use host='0.0.0.0' to make it accessible from other devices on your network
    # (like your PHP script if running via XAMPP/MAMP on the same machine)
    app.run(debug=True, host='0.0.0.0', port=5001) # debug=True provides auto-reload and more error details during development
