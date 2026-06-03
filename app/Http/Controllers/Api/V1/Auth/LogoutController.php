<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function store(Request $request)
    {
        $token = $request->user()->token();

        $token->revoke();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }
}