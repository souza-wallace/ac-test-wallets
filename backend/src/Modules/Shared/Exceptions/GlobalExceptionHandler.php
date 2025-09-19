<?php

namespace Modules\Shared\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GlobalExceptionHandler
{
    public static function handle(\Throwable $exception, bool $exposeDetails = false): JsonResponse
    {
        $shouldExpose = $exposeDetails;
        
        return match (true) {
            $exception instanceof ValidationException => self::handleValidation($exception, $shouldExpose),
            $exception instanceof NotFoundHttpException => self::handleNotFound($exception, $shouldExpose),
            $exception instanceof QueryException => self::handleDatabase($exception, $shouldExpose),
            $exception instanceof DomainException => self::handleDomain($exception, $shouldExpose),
            default => self::handleGeneral($exception, $shouldExpose)
        };
    }

    private static function handleValidation(ValidationException $exception, bool $shouldExpose): JsonResponse
    {
        self::logException('Validation error', $exception, 'warning', ['errors' => $exception->errors()]);
        
        $response = [
            'error' => 'Validation failed',
            'message' => 'The given data was invalid'
        ];
        
        if ($shouldExpose) {
            $response = array_merge($response, self::getExceptionDetails($exception));
        }
        return response()->json($response, 422);
    }

    private static function handleNotFound(NotFoundHttpException $exception, bool $shouldExpose): JsonResponse
    {
        self::logException('Resource not found', $exception, 'info');
        $response = [
            'error' => 'Resource not found',
            'message' => 'The requested resource was not found'
        ];

        if ($shouldExpose) {
            $response = array_merge($response, self::getExceptionDetails($exception));
        }

        return response()->json($response, 404);
    }

    private static function handleDatabase(QueryException $exception, bool $shouldExpose): JsonResponse
    {
        self::logException('Database error', $exception, 'error');
        
        $response = [
            'error' => 'Database error',
            'message' => 'An error occurred while processing your request'
        ];
        
        if ($shouldExpose) {
            $response = array_merge($response, self::getExceptionDetails($exception));
        }
        
        return response()->json($response, 500);
    }

    private static function handleDomain(DomainException $exception, bool $shouldExpose): JsonResponse
    {
        self::logException('Domain exception', $exception, 'warning');
        $response = [
            'error' => 'Domain exception',
            'message' => $exception->getMessage()
        ];
        if ($shouldExpose) {
            $response = array_merge($response, self::getExceptionDetails($exception));
        }
        return response()->json($response, 400);
    }

    private static function handleGeneral(\Throwable $exception, bool $shouldExpose): JsonResponse
    {
        self::logException('General error', $exception, 'error');
        
        $response = [
            'error' => 'Internal server error',
            'message' => 'An unexpected error occurred'
        ];
        
        if ($shouldExpose) {
            $response = array_merge($response, self::getExceptionDetails($exception));
        }
        
        return response()->json($response, 500);
    }

    private static function logException(string $context, \Throwable $exception, string $level, array $extra = []): void
    {
        $logData = array_merge([
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode()
        ], $extra);
        
        logger()->{$level}($context, $logData);
    }

    private static function getExceptionDetails(\Throwable $exception): array
    {
        return [
            'details' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ];
    }
}