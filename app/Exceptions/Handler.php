<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                return $this->renderApiException($e);
            }
        });
    }
    protected function renderApiException(Throwable $e)
    {
        $map = [
            ValidationException::class => function ($e) {
                $errors = $e->errors();
                $firstError = collect($errors)->flatten()->first();
                return $this->apiResponse([], 422, $firstError, $errors);
            },
            AuthenticationException::class => fn() => $this->apiResponse([], 406, 'Unauthenticated'),
            NotFoundHttpException::class => fn() => $this->apiResponse([], 404, 'Not Found'),
            RouteNotFoundException::class => fn() => $this->apiResponse([], 404, 'Route Not Found'),
        ];

        foreach ($map as $type => $handler) {
            if ($e instanceof $type) {
                return $handler($e);
            }
        }

        if ($e instanceof HttpException) {
            if ($e->getStatusCode() == 421) {
                return $this->apiResponse([], 421, __('Please Go to Store to update Application'));
            }
            if ($e->getStatusCode() == 405) {
                return $this->apiResponse([], 405, __('Network Error'));
            }
            return $this->apiResponse([], $e->getStatusCode());
        }

        return $this->apiResponse([], 500, __('Sorry, Please try your request again'), $e->getMessage());
    }
}
