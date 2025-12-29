<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {

            return match (true) {

                $e instanceof ValidationException =>
                $this->validationError($e->errors()),

                $e instanceof AuthenticationException =>
                $this->unauthorized(),

                $e instanceof ModelNotFoundException =>
                $this->notFound(),

                default => $this->serverError(),
            };
        }

        return parent::render($request, $e);
    }
}
