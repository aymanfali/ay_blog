<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    protected function respond(
        bool $success,
        mixed $data = null,
        ?string $messageKey = null,
        array $replace = [],
        int $status = Response::HTTP_OK,
        ?array $errors = null,
        ?array $meta = null
    ): JsonResponse {
        $locale = App::getLocale();

        return response()->json([
            'success' => $success,
            'message' => $messageKey
                ? __($messageKey, $replace, $locale)
                : ($success ? __('messages.success', [], $locale) : null),
            'locale'  => $locale,
            'data'    => $success ? $data : null,
            'errors'  => $success ? null : $errors,
            'meta'    => [
                'timestamp' => now()->toISOString(),
                'status'    => $status,
                ...($meta ?? []),
            ],
        ], $status);
    }

    /* =========================
     | SUCCESS RESPONSES
     |=========================*/

    protected function ok(mixed $data = null, ?string $messageKey = null): JsonResponse
    {
        return $this->respond(true, $data, $messageKey, [], Response::HTTP_OK);
    }

    protected function created(mixed $data, ?string $messageKey = null): JsonResponse
    {
        return $this->respond(true, $data, $messageKey, [], Response::HTTP_CREATED);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    protected function deletedResponse(mixed $data = null, ?array $meta = null, ?string $messageKey = null): JsonResponse
    {
        return $this->respond(true, $data, $messageKey ?? 'messages.deleted', [], Response::HTTP_OK, null, $meta);
    }

    /* =========================
     | CLIENT ERRORS
     |=========================*/

    protected function badRequest(string $messageKey = 'messages.validation_failed', array $errors = []): JsonResponse
    {
        return $this->respond(false, null, $messageKey, [], Response::HTTP_BAD_REQUEST, $errors);
    }

    protected function validationErrorResponse(array $errors): JsonResponse
    {
        return $this->respond(false, null, 'messages.validation_failed', [], Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    protected function validationError(array $errors): JsonResponse
    {
        return $this->validationErrorResponse($errors);
    }

    protected function unauthorized(string $messageKey = 'messages.unauthorized'): JsonResponse
    {
        return $this->respond(false, null, $messageKey, [], Response::HTTP_UNAUTHORIZED);
    }

    protected function forbidden(string $messageKey = 'messages.forbidden'): JsonResponse
    {
        return $this->respond(false, null, $messageKey, [], Response::HTTP_FORBIDDEN);
    }

    protected function notFoundResponse(string $messageKey = 'messages.not_found'): JsonResponse
    {
        return $this->respond(false, null, $messageKey, [], Response::HTTP_NOT_FOUND);
    }

    protected function notFound(string $messageKey = 'messages.not_found'): JsonResponse
    {
        return $this->notFoundResponse($messageKey);
    }

    /* =========================
     | SERVER ERRORS
     |=========================*/

    protected function serverError(string $messageKey = 'messages.server_error'): JsonResponse
    {
        return $this->respond(false, null, $messageKey, [], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
