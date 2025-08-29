<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{   
    /**
     * Handle user login.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            
            // Find the user by email
            $user = User::where('email', $request->email)->first();

            // Check if user exists and password matches
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'These credentials do not match our records.'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Generate a new token for the user
            $token = $user->createToken('user-token')->plainTextToken;

            // Return the response with user data and token
            $response = [
                'success'       => true,
                'status'        => Response::HTTP_OK,
                'message'       => 'Login successful',
                'token'         => $token,
                'type'          => "Bearer",
                'user'          => $user->load('categories'),
            ];

            // Return the response with a 200 status code
            return response()->json($response, Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => $exception], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle user logout.
     *
     * @return JsonResponse
     */
    public function logout() : JsonResponse
    {
        Auth::user()->tokens->each(function ($token) {
            $token->forceDelete();
        });

        // Return a success response
        return response()->json([
            'message' => 'Logged out successfully',
        ], Response::HTTP_OK);
    }
}
