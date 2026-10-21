
from flask import Blueprint

from app.controllers.review_controller import (
    create_review_controller,
    get_product_reviews_controller,
    get_review_controller,
    update_review_controller,
    delete_review_controller
)


review_bp = Blueprint("reviews", __name__)


@review_bp.route("/", methods=["POST"])
def create_review():
    return create_review_controller()


@review_bp.route("/product/<product_id>", methods=["GET"])
def get_product_reviews(product_id):
    return get_product_reviews_controller(product_id)


@review_bp.route("/<review_id>", methods=["GET"])
def get_review(review_id):
    return get_review_controller(review_id)


@review_bp.route("/<review_id>", methods=["PUT"])
def update_review(review_id):
    return update_review_controller(review_id)


@review_bp.route("/<review_id>", methods=["DELETE"])
def delete_review(review_id):
    return delete_review_controller(review_id)
