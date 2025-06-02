<?php

namespace app\exceptions;

use yii\web\HttpException;

class ValidationException extends HttpException
{
    public function __construct(array $errors, string $message = 'Erro de validação.', int $statusCode = 400)
    {
        parent::__construct($statusCode, json_encode([
            'message' => $message,
            'errors' => $errors
        ], JSON_UNESCAPED_UNICODE));
    }
}
