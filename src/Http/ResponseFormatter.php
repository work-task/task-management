<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ResponseFormatter
{
    private const SUCCESS = 0;
    private const ERROR = -1;

    /**
     * @param array<mixed>|null $data
     */
    public static function success(?array $data = null, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse([
            'success' => self::SUCCESS,
            'data' => $data,
            'validation_errors' => null,
        ], $status);
    }

    /**
     * @param array<mixed> $errors
     */
    public static function errors(array $errors, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse([
            'success' => self::ERROR,
            'data' => null,
            'validation_errors' => $errors,
        ], $status);
    }

    public static function errorFromConstraintList(
        ConstraintViolationListInterface $constraintViolationList,
        int $status = Response::HTTP_OK,
    ): JsonResponse {
        $errors = [];
        foreach ($constraintViolationList as $constraintViolation) {
            $errors[$constraintViolation->getPropertyPath()] = $constraintViolation->getMessage();
        }

        return self::success($errors, $status);
    }
}
