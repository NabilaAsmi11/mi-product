
import os
import requests


PRODUCT_SERVICE_URL = os.getenv(
    "PRODUCT_SERVICE_URL",
    "http://product-service:3001"
)


def get_product(product_id):
    try:
        response = requests.get(
            f"{PRODUCT_SERVICE_URL}/products/{product_id}",
            timeout=3
        )

        if response.status_code == 404:
            return None, "not_found"

        if not response.ok:
            return None, "unavailable"

        return response.json(), None

    except requests.RequestException:
        return None, "unavailable"
