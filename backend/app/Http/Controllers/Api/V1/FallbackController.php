<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class FallbackController extends Controller
{
    use ApiResponseTrait;

    public function __invoke()
    {
        return $this->notFound('messages.not_found');
    }
}
