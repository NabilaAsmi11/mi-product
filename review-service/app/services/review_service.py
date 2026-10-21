
from app.models.review_model import (
    create_review,
    get_reviews_by_product,
    get_review_by_id,
    update_review,
    delete_review
)
from app.schemas.review_schema import validate_review
from app.clients.product_client import get_product


def add_review(data):
    # Validasi data review
    is_valid, error = validate_review(data)

    if not is_valid:
        return {
            "success": False,
            "message": error
        }, 400

    # Pastikan produk tersedia di Product Service
    product, product_error = get_product(data["product_id"])

    if product_error == "not_found":
        return {
            "success": False,
            "message": "Produk tidak ditemukan"
        }, 422

    if product_error == "unavailable":
        return {
            "success": False,
            "message": "Product Service tidak dapat dihubungi"
        }, 503

    # Simpan review jika produk valid
    review = create_review(data)

    return {
        "success": True,
        "message": "Review berhasil ditambahkan",
        "data": review
    }, 201


def list_product_reviews(product_id):
    try:
        product_id = int(product_id)
    except (ValueError, TypeError):
        return {
            "success": False,
            "message": "product_id harus berupa angka bulat positif"
        }, 400

    if product_id < 1:
        return {
            "success": False,
            "message": "product_id harus berupa angka bulat positif"
        }, 400

    reviews = get_reviews_by_product(product_id)

    average_rating = (
        round(sum(item["rating"] for item in reviews) / len(reviews), 2)
        if reviews else 0
    )

    return {
        "success": True,
        "data": reviews,
        "summary": {
            "total_reviews": len(reviews),
            "average_rating": average_rating
        }
    }, 200


def detail_review(review_id):
    review = get_review_by_id(review_id)

    if not review:
        return {
            "success": False,
            "message": "Review tidak ditemukan"
        }, 404

    return {
        "success": True,
        "data": review
    }, 200


def edit_review(review_id, data):
    if not isinstance(data, dict):
        return {
            "success": False,
            "message": "Request body harus berupa JSON object"
        }, 400

    # PUT hanya boleh mengubah rating dan comment
    allowed_fields = {"rating", "comment"}

    if any(field not in allowed_fields for field in data):
        return {
            "success": False,
            "message": "PUT hanya boleh mengubah rating dan comment"
        }, 400

    is_valid, error = validate_review(data, partial=True)

    if not is_valid:
        return {
            "success": False,
            "message": error
        }, 400

    existing_review = get_review_by_id(review_id)

    if not existing_review:
        return {
            "success": False,
            "message": "Review tidak ditemukan"
        }, 404

    review = update_review(review_id, data)

    if not review:
        return {
            "success": False,
            "message": "Review gagal diperbarui"
        }, 500

    return {
        "success": True,
        "message": "Review berhasil diperbarui",
        "data": review
    }, 200


def remove_review(review_id):
    deleted = delete_review(review_id)

    if not deleted:
        return {
            "success": False,
            "message": "Review tidak ditemukan"
        }, 404

    return {
        "success": True,
        "message": "Review berhasil dihapus"
    }, 200
