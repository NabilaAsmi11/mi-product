
def validate_review(data, partial=False):
    if not isinstance(data, dict):
        return False, "Request body harus berupa JSON object"

    required_fields = ["product_id", "user_id", "rating"]

    if not partial:
        for field in required_fields:
            if field not in data:
                return False, f"Field {field} wajib diisi"

    # Validasi ID produk
    if "product_id" in data:
        if type(data["product_id"]) is not int or data["product_id"] < 1:
            return False, "product_id harus berupa angka bulat minimal 1"

    # Validasi ID pengguna
    if "user_id" in data:
        if type(data["user_id"]) is not int or data["user_id"] < 1:
            return False, "user_id harus berupa angka bulat minimal 1"

    # Validasi rating
    if "rating" in data:
        if type(data["rating"]) is not int or not 1 <= data["rating"] <= 5:
            return False, "rating harus berupa angka bulat antara 1 sampai 5"

    # Validasi komentar
    if "comment" in data:
        comment = data["comment"]

        if not isinstance(comment, str):
            return False, "comment harus berupa teks"

        if len(comment) > 1000:
            return False, "comment maksimal 1000 karakter"

    if partial and not any(
        field in data for field in ["rating", "comment"]
    ):
        return False, "Rating atau comment harus diisi untuk mengubah review"

    return True, None
