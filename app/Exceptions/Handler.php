<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

/**
 * Class Handler
 * @package App\Exceptions
 */
final class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [

    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->renderable(function (AuthorizationException $exception, $request) {
            return $this->forbidden($request, $exception);
        });

        $this->renderable(function (AccessDeniedHttpException $exception, $request) {
            return $this->forbidden($request, $exception);
        });

        $this->reportable(function (Throwable $e) {

        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if (\method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::toResponse($request, $response);
        } elseif ($e instanceof Responsable) {
            return $e->toResponse($request);
        }

        $e = $this->prepareException($this->mapException($e));

        foreach ($this->renderCallbacks as $renderCallback) {
            if (\is_a($e, $this->firstClosureParameterType($renderCallback))) {
                $response = $renderCallback($e, $request);

                if (!\is_null($response)) {
                    return $response;
                }
            }
        }

        $expectsJson = $request->expectsJson() || 0 === \strpos($request->path(), 'api');

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } else if ($e instanceof UnauthorizedHttpException) {
            return $this->basicAuthFailed($request, $e);
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        } elseif ($e instanceof BadRequestHttpException && $expectsJson) {
            return $this->badRequest($request, $e);
        }

        return $expectsJson
            ? $this->prepareJsonResponse($request, $e)
            : $this->prepareResponse($request, $e);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Symfony\Component\HttpKernel\Exception\BadRequestHttpException $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function badRequest($request, $exception)
    {
        return \response()->json([
            'code' => 40000,
            'message' => $exception->getMessage(),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function basicAuthFailed($request, UnauthorizedHttpException $exception)
    {
        return \response('', $exception->getStatusCode(), $exception->getHeaders());
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // if request expects json content
        // or request path start with api
        return $request->expectsJson() || 0 === \strpos($request->path(), 'api')
            ? \response()->json([
                'code' => 40100,
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNAUTHORIZED)
            : \redirect()->guest($exception->redirectTo() ?? \route('login'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\Access\AuthorizationException|\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function forbidden($request, $exception)
    {
        // if request expects json content
        // or request path start with api
        return $request->expectsJson() || 0 === \strpos($request->path(), 'api')
            ? \response()->json([
                'code' => 40300,
                'message' => $exception->getMessage(),
            ], Response::HTTP_FORBIDDEN)
            : $this->prepareResponse($request, $exception);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param \Illuminate\Validation\ValidationException $e
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        if (false === ($request->expectsJson() || 0 === \strpos($request->path(), 'api'))) {
            return $this->invalid($request, $e);
        }

        return \response()->json([
            'code' => 42200,
            'message' => \__('messages.exception.given_data_invalid'),
            'errors' => $this->formatErrorMessages($e->errors()),
        ], $e->status);
    }

    /**
     * @param array $errors
     *
     * @return array
     */
    protected function formatErrorMessages(array $errors): array
    {
        return \array_map('array_shift', $errors);
    }

    /**
     * Convert a validation exception into a response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Validation\ValidationException $exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function invalid($request, ValidationException $exception)
    {
        return \redirect($exception->redirectTo ?? 'home')
            ->withInput(Arr::except($request->input(), $this->dontFlash))
            ->withErrors($exception->errors(), $request->input('_error_bag', $exception->errorBag))
        ;
    }
}
