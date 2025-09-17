<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        // Simple response without any logic for now
        return response()->json([
            'success' => true,
            'message' => 'RegisterController created successfully',
            'data' => [
                'received_data' => $request->all()
            ]
        ], 201);
    }
}
