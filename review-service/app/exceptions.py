
class ReviewServiceException(Exception):
    def __init__(self, message, status_code=400):
        super().__init__(message)
        self.message = message
        self.status_code = status_code


class ProductNotFoundException(ReviewServiceException):
    def __init__(self, message="Produk tidak ditemukan"):
        super().__init__(message, 422)


class ProductServiceUnavailableException(ReviewServiceException):
    def __init__(self, message="Product Service tidak dapat dihubungi"):
        super().__init__(message, 503)
