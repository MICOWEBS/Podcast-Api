<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $errors;

    public function __construct(string $message, array $errors = [], int $code = 400)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function render($request)
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->getMessage(),
            'errors' => $this->getErrors(),
            'code' => $this->getCode()
        ], $this->getCode());
    }
} 