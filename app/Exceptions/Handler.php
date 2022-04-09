<?php

namespace App\Exceptions;

use Helper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
    }
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof MethodNotAllowedHttpException) {

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => Helper::ApiFailedStatus(),
                    'message' => "Method not allowed",
                ], 404);
            }
            // return redirect()->back()->with('error','Method not allowed');
        }

        if ($exception instanceof ModelNotFoundException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => Helper::ApiFailedStatus(),
                    'message' => 'Entry for ' . str_replace('App\\', '', $exception->getModel()) . ' not found'], 404);
            }
            // return redirect()->back()->with('error','Entry for ' . str_replace('App\\', '', $exception->getModel()) . ' not found');
        }

        if ($exception instanceof \BadMethodCallException) {
            if ($request->expectsJson()) {
                return response()->json(['status' => Helper::ApiFailedStatus(), 'message' => 'Class not Found ' . str_replace('App\\', '', $exception->getMessage())], 404);
            }
            // return redirect()->back()->with('error','Class not Found ' . str_replace('App\\', '', $exception->getMessage()));
        }
        if ($exception instanceof \Illuminate\Contracts\Container\BindingResolutionException) {
            if ($request->expectsJson()) {
                return response()->json(['status' => Helper::ApiFailedStatus(), 'message' => 'Class not Found ' . str_replace('App\\', '', $exception->getMessage())], 404);
            }
            // return redirect()->back()->with('error','Class not Found ' . str_replace('App\\', '', $exception->getMessage()));
        }
        if ($exception instanceof \Symfony\Component\Mailer\Exception\TransportException) {
            if ($request->expectsJson()) {
                return response()->json(['status' => Helper::ApiFailedStatus(), 'message' => $exception->getMessage()], 500);
            }
            // return redirect()->back()->with('error','Class not Found ' . str_replace('App\\', '', $exception->getMessage()));
        }
        if ($exception instanceof \ErrorException) {
            if ($request->expectsJson()) {
                return response()->json(['status' => Helper::ApiFailedStatus(), 'message' => $exception->getMessage()], 500);
            }
            // return redirect()->back()->with('error','Class not Found ' . str_replace('App\\', '', $exception->getMessage()));
        }
        if ($exception instanceof \Error) {
            if ($request->expectsJson()) {
                return response()->json(['status' => Helper::ApiFailedStatus(), 'message' => $exception->getMessage()], 500);
            }
            // return redirect()->back()->with('error','Class not Found ' . str_replace('App\\', '', $exception->getMessage()));
        }
        if ($exception instanceof \Illuminate\Database\QueryException) {
            if ($request->expectsJson()) {
                return response()->json(['status' => Helper::ApiFailedStatus(), 'message' => $exception->getMessage()], 500);
            }
            // return redirect()->back()->with('error','Class not Found ' . str_replace('App\\', '', $exception->getMessage()));
        }
        if ($exception instanceof \Exception) {
            if ($request->expectsJson()) {
                return response()->json(['status' => Helper::ApiFailedStatus(), 'message' => $exception->getMessage()], 500);
            }
            // return redirect()->back()->with('error','Class not Found ' . str_replace('App\\', '', $exception->getMessage()));
        }
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            if ($request->expectsJson()) {
                return response()->json(['status' => Helper::ApiFailedStatus(), 'message' => $exception->getMessage()], 500);
            }
            // return redirect()->back()->with('error','Class not Found ' . str_replace('App\\', '', $exception->getMessage()));
        }
        return parent::render($request, $exception);
    }
}
