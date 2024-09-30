<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\JsonResponse;

class DocumentException extends Exception implements ShouldntReport
{
    public static function notFound(): self
    {
        return new self('Document not found', 404);
    }

     public function render():  JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->getMessage()
        ], $this->getCode());
    }
}
