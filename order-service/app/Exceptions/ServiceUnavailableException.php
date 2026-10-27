<?php

namespace App\Exceptions;

use Exception;

class ServiceUnavailableException extends Exception
{
    // Exception untuk menangani jika service lain (product/cart) mati atau timeout
}