<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = '', int $code = 200): JsonResponse
    {
        $response = ['success' => true];

        if ($message !== '') {
            $response['message'] = $message;
        }

        $response['data'] = $data;

        return response()->json($response, $code);
    }

    protected function created(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function error(string $message, int $code = 500, mixed $data = null): JsonResponse
    {
        $response = ['success' => false, 'message' => $message];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    protected function notFound(string $message = 'Not found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, 401);
    }

    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, 403);
    }

    protected function serverError(string $message = 'Server error'): JsonResponse
    {
        return $this->error($message, 500);
    }

    protected function paginated(mixed $data, object $paginator, string $message = ''): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $data,
            'pagination' => [
                'current_page'  => $paginator->currentPage(),
                'per_page'      => $paginator->perPage(),
                'total'         => $paginator->total(),
                'last_page'     => $paginator->lastPage(),
                'from'          => $paginator->firstItem(),
                'to'            => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
            ],
        ];

        if ($message !== '') {
            $response['message'] = $message;
        }

        return response()->json($response, 200);
    }
}
