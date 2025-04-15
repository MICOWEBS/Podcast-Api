<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): JsonResponse
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions and return appropriate JSON responses.
     */
    private function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($e);
        }

        if ($e instanceof AuthenticationException) {
            return $this->handleAuthenticationException($e);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->handleNotFoundHttpException($e);
        }

        if ($e instanceof HttpException) {
            return $this->handleHttpException($e);
        }

        return $this->handleGenericException($e);
    }

    /**
     * Handle validation exceptions.
     */
    private function handleValidationException(ValidationException $e): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'The given data was invalid.',
            'errors' => $e->errors(),
            'code' => 422
        ], 422);
    }

    /**
     * Handle model not found exceptions.
     */
    private function handleModelNotFoundException(ModelNotFoundException $e): JsonResponse
    {
        $model = strtolower(class_basename($e->getModel()));
        return response()->json([
            'status' => 'error',
            'message' => "The requested {$model} could not be found.",
            'code' => 404
        ], 404);
    }

    /**
     * Handle authentication exceptions.
     */
    private function handleAuthenticationException(AuthenticationException $e): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated.',
            'code' => 401
        ], 401);
    }

    /**
     * Handle not found HTTP exceptions.
     */
    private function handleNotFoundHttpException(NotFoundHttpException $e): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'The requested resource could not be found.',
            'code' => 404
        ], 404);
    }

    /**
     * Handle HTTP exceptions.
     */
    private function handleHttpException(HttpException $e): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage() ?: 'An error occurred.',
            'code' => $e->getStatusCode()
        ], $e->getStatusCode());
    }

    /**
     * Handle generic exceptions.
     */
    private function handleGenericException(Throwable $e): JsonResponse
    {
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        $message = $e->getMessage() ?: 'An unexpected error occurred.';

        if (config('app.debug')) {
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'code' => $statusCode,
                'debug' => [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace()
                ]
            ], $statusCode);
        }

        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => $statusCode
        ], $statusCode);
    }
} 