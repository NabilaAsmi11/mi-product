
import os

from flask import Flask, jsonify
from dotenv import load_dotenv

from app.database import check_database_connection
from app.routes.review_routes import review_bp


load_dotenv()

app = Flask(__name__)

# Daftarkan routes Review Service
app.register_blueprint(review_bp, url_prefix="/api/reviews")


@app.route("/health", methods=["GET"])
def health():
    database_connected = check_database_connection()

    return jsonify({
        "status": "healthy" if database_connected else "unhealthy",
        "database": "connected" if database_connected else "disconnected"
    }), 200 if database_connected else 503


if __name__ == "__main__":
    host = os.getenv("HOST", "0.0.0.0")
    port = int(os.getenv("PORT", "3002"))

    app.run(host=host, port=port, debug=False)
