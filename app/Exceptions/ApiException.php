<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $errors;
    protected $statusCode;

    public function __construct(string $message = '', array $errors = [], int $statusCode = 400)
    {
        parent::__construct($message);
        $this->errors = $errors;
        $this->statusCode = $statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function render()
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->getMessage(),
            'errors' => $this->getErrors(),
            'code' => $this->getStatusCode()
        ], $this->getStatusCode());
    }
} 