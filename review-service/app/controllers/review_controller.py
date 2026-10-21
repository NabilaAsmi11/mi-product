
from flask import request, jsonify

from app.services.review_service import (
    add_review,
    list_product_reviews,
    detail_review,
    edit_review,
    remove_review
)


def create_review_controller():
    data = request.get_json(silent=True)

    if data is None:
        return jsonify({
            "success": False,
            "message": "Request body harus berupa JSON"
        }), 400

    result, status_code = add_review(data)
    return jsonify(result), status_code


def get_product_reviews_controller(product_id):
    result, status_code = list_product_reviews(product_id)
    return jsonify(result), status_code


def get_review_controller(review_id):
    result, status_code = detail_review(review_id)
    return jsonify(result), status_code


def update_review_controller(review_id):
    data = request.get_json(silent=True)

    if data is None:
        return jsonify({
            "success": False,
            "message": "Request body harus berupa JSON"
        }), 400

    result, status_code = edit_review(review_id, data)
    return jsonify(result), status_code


def delete_review_controller(review_id):
    result, status_code = remove_review(review_id)
    return jsonify(result), status_code
