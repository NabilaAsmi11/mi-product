
from datetime import datetime, timezone

from bson import ObjectId
from pymongo import ReturnDocument

from app.database import db


reviews_collection = db["reviews"]


def serialize_review(review):
    if not review:
        return None

    return {
        "id": str(review["_id"]),
        "product_id": review["product_id"],
        "user_id": review["user_id"],
        "rating": review["rating"],
        "comment": review.get("comment", ""),
        "created_at": review["created_at"].isoformat(),
        "updated_at": review["updated_at"].isoformat()
    }


def create_review(data):
    now = datetime.now(timezone.utc)

    review = {
        "product_id": data["product_id"],
        "user_id": data["user_id"],
        "rating": data["rating"],
        "comment": data.get("comment", ""),
        "created_at": now,
        "updated_at": now
    }

    result = reviews_collection.insert_one(review)
    review["_id"] = result.inserted_id

    return serialize_review(review)


def get_reviews_by_product(product_id):
    reviews = reviews_collection.find(
        {"product_id": product_id}
    ).sort("created_at", -1)

    return [serialize_review(review) for review in reviews]


def get_review_by_id(review_id):
    if not ObjectId.is_valid(review_id):
        return None

    review = reviews_collection.find_one(
        {"_id": ObjectId(review_id)}
    )

    return serialize_review(review)


def update_review(review_id, data):
    if not ObjectId.is_valid(review_id):
        return None

    updates = {}

    if "rating" in data:
        updates["rating"] = data["rating"]

    if "comment" in data:
        updates["comment"] = data["comment"]

    if not updates:
        return None

    updates["updated_at"] = datetime.now(timezone.utc)

    review = reviews_collection.find_one_and_update(
        {"_id": ObjectId(review_id)},
        {"$set": updates},
        return_document=ReturnDocument.AFTER
    )

    return serialize_review(review)


def delete_review(review_id):
    if not ObjectId.is_valid(review_id):
        return False

    result = reviews_collection.delete_one(
        {"_id": ObjectId(review_id)}
    )

    return result.deleted_count > 0
