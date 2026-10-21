
import os
from pymongo import MongoClient

MONGO_URI = os.getenv(
    "MONGO_URI",
    "mongodb://localhost:27017/"
)

MONGO_DB_NAME = os.getenv(
    "MONGO_DB_NAME",
    "reviewdb"
)

client = MongoClient(
    MONGO_URI,
    serverSelectionTimeoutMS=5000
)

db = client[MONGO_DB_NAME]


def check_database_connection():
    try:
        client.admin.command("ping")
        return True
    except Exception as error:
        print(f"Koneksi MongoDB gagal: {error}")
        return False
