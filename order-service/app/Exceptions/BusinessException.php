<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    // Exception untuk menangani aturan bisnis (misal: stok habis, keranjang kosong)
}