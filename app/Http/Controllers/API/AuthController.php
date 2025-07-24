<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @OA\Info(title="API Documentation", version="1.0")
 */
class AuthController extends Controller
{
    /**
     * Register a new user
     */

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="Creates a new user account and returns an auth token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="role", type="string", example="user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="token", type="string", example="token_abc123"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Registration failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Registration failed"),
     *             @OA\Property(property="error", type="string", example="Something went wrong")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'user',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="User login",
     *     description="Authenticates a user and returns a Bearer token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="remember", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", type="object"),  
     *                 @OA\Property(property="token", type="string", example="token_abc123"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account is deactivated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Your account has been deactivated. Please contact support.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Login failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="No account found with this email address.")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The provided password is incorrect."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred during login"),
     *             @OA\Property(property="error", type="string", example="Unexpected exception")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        try {
            // Check if user exists and is active
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['No account found with this email address.']
                ]);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact support.'
                ], 403);
            }

            // Attempt to authenticate
            if (!Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'password' => ['The provided password is incorrect.']
                ]);
            }

            // Delete existing tokens if not remember me
            if (!$request->remember) {
                $user->tokens()->delete();
            }

            // Create new token
            $tokenName = 'auth_token_' . now()->timestamp;
            $token = $user->createToken($tokenName)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => new UserResource($request->user())
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'avatar' => ['sometimes', 'string', 'max:255'],
                'current_password' => ['required_with:password'],
                'password' => ['sometimes', 'confirmed', 'min:6'],
            ]);

            // Check current password if updating password
            if (isset($validated['password'])) {
                if (!Hash::check($validated['current_password'], $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect',
                        'errors' => ['current_password' => ['Current password is incorrect']]
                    ], 422);
                }
                $validated['password'] = Hash::make($validated['password']);
            }

            // Remove current_password from update data
            unset($validated['current_password']);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => new UserResource($user->fresh())
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Auth"},
     *     summary="Logout current user",
     *     description="Deletes the current user's access token (logout only from current device).",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Logout failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Logout failed"),
     *             @OA\Property(property="error", type="string", example="Something went wrong")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {
            // Delete current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        try {
            // Delete all tokens
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out from all devices successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     tags={"Auth"},
     *     summary="Refresh authentication token",
     *     description="Deletes the current token and issues a new access token for the authenticated user.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", type="object"),  
     *                 @OA\Property(property="token", type="string", example="new_token_abc123"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Token refresh failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Token refresh failed"),
     *             @OA\Property(property="error", type="string", example="Something went wrong")
     *         )
     *     )
     * )
     */
    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            
            // Delete current token
            $request->user()->currentAccessToken()->delete();
            
            // Create new token
            $token = $user->createToken('auth_token_' . now()->timestamp)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function googleLogin(Request $request)
    // {
    //     $setCorsHeaders = function($response) {
    //         $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
    //         $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //         $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    //         return $response;
    //     };

    //     $request->validate([
    //         'token' => 'required|string',
    //     ]);
        
    //     try {
    //         // Return token as response first
    //         // $response = response()->json(['token' => $request->token]);

    //         // Socialite 5.x - stateless() method removed
    //         $googleUser = Socialite::driver('google')->userFromToken($request->token);

    //          $response = response()->json(['userdata' => $googleUser]);

    //           return $setCorsHeaders($response);

    //         // Optional: Verify the token is valid
    //         if (!$googleUser->getId()) {
    //             throw new \Exception('Invalid Google user data');
    //         }

    //         // Check if user exists with improved error handling
    //         $user = User::firstOrCreate(
    //             ['email' => $googleUser->getEmail()],
    //             [
    //                 'name' => $googleUser->getName(),
    //                 'last_name' => '', // Google API might not split names
    //                 'avatar' => $googleUser->getAvatar(),
    //                 'password' => bcrypt(Str::random(16)),
    //                 'email_verified_at' => now(),
    //                 'status' => 'active',
    //             ]
    //         );

    //         // Laravel 11 token creation with expiration
    //         $token = $user->createToken(
    //             name: 'auth_token',
    //             abilities: ['*'],
    //             expiresAt: now()->addDays(30)
    //         )->plainTextToken;

    //         return response()->json([
    //             'token' => $token,
    //             'user' => $user->only(['id', 'name', 'email', 'avatar', 'status']), // Only return needed fields
    //             'token_type' => 'Bearer',
    //             'expires_at' => now()->addDays(30)->toISOString()
    //         ]);

    //     } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
    //         return response()->json([
    //             'error' => 'Invalid Google token',
    //             'message' => 'The provided token is invalid or expired'
    //         ], 401);
    //     } catch (\Exception $e) {
    //         // Log the error for debugging
    //         \Log::error('Google authentication failed', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return response()->json([
    //             'error' => 'Google authentication failed',
    //             'message' => $e->getMessage()
    //         ], 401);
    //     }
    // }

 public function googleLogin(Request $request)
{
    // $setCorsHeaders = function($response) {
    //     $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
    //     $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //     $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    //     return $response;
    // };

    $request->validate([
        'token' => 'required|string',
    ]);

    // dd("token value ======", $request->token);
    
    try {
        // Decode the JWT ID token from Google One Tap
        $googleUser = $this->verifyGoogleIdToken($request->token);
        
        if (!$googleUser) {
            // return $setCorsHeaders(response()->json([
            return response()->json([
                'error' => 'Invalid Google token',
                'message' => 'The provided token is invalid or expired'
            ], 401);
        }

        // Check if user exists with improved error handling
        $user = User::firstOrCreate(
            ['email' => $googleUser['email']],
            [
                'name' => $googleUser['name'],
                'last_name' => $googleUser['family_name'] ?? '',
                'avatar' => $googleUser['picture'] ?? '',
                'password' => bcrypt(Str::random(16)),
                'email_verified_at' => now(),
                'status' => 'active',
                'login_type' => 'google',
            ]
        );

        // Laravel 11 token creation with expiration
        $tokenName = 'auth_token_' . now()->timestamp;
        $token = $user->createToken(
            name: $tokenName,
            abilities: ['*'],
            expiresAt: now()->addDays(30)
        )->plainTextToken;

        $response = response()->json([
            'success' => true,
            'message' => 'Google login successful',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);

        // return $setCorsHeaders($response); 
        return $response; 
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Google authentication failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        $response = response()->json([
            'error' => 'Google authentication failed',
            'message' => $e->getMessage()
        ], 401);

        // return $setCorsHeaders($response);
        return $response;
    }
}

/**
 * Verify Google ID Token and return user data (without Google API client)
 */
private function verifyGoogleIdToken($idToken)
{
    try {
        // Verify token with Google's tokeninfo endpoint
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken
        ]);
        
        if ($response->successful()) {
            $payload = $response->json();
            
            // Verify the token is for your app
            if ($payload['aud'] !== config('services.google.client_id')) {
                return null;
            }
            
            return $payload;
        }
        
        return null;
    } catch (\Exception $e) {
        Log::error('Google ID token verification failed', ['error' => $e->getMessage()]);
        return null;
    }
}
//     public function googleLogin(Request $request)
// {
//     $setCorsHeaders = function($response) {
//         $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
//         $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
//         $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
//         return $response;
//     };

//     $request->validate([
//         'token' => 'required|string',
//     ]);
    
//     try {
//         // Decode the JWT ID token from Google One Tap
//         $googleUser = $this->verifyGoogleIdToken($request->token);
        
//         if (!$googleUser) {
//             return $setCorsHeaders(response()->json([
//                 'success' => false,
//                 'message' => 'Invalid Google token',
//                 'error' => 'The provided token is invalid or expired'
//             ], 401));
//         }

//         // Check if user exists with improved error handling
//         $user = User::firstOrCreate(
//             ['email' => $googleUser['email']],
//             [
//                 'name' => $googleUser['name'],
//                 'last_name' => $googleUser['family_name'] ?? '',
//                 'avatar' => $googleUser['picture'] ?? '',
//                 'password' => bcrypt(Str::random(16)),
//                 'email_verified_at' => now(),
//                 'status' => 'active',
//             ]
//         );

//         // Check if user is active (same as regular login)
//         if ($user->status !== 'active') {
//             return $setCorsHeaders(response()->json([
//                 'success' => false,
//                 'message' => 'Your account has been deactivated. Please contact support.'
//             ], 403));
//         }

//         // Delete existing tokens (optional, based on your preference)
//         // $user->tokens()->delete();

//         // Laravel 11 token creation with expiration
//         $tokenName = 'auth_token_' . now()->timestamp;
//         $token = $user->createToken(
//             name: $tokenName,
//             abilities: ['*'],
//             expiresAt: now()->addDays(30)
//         )->plainTextToken;

//         // Return response in the same format as regular login
//         $response = response()->json([
//             'success' => true,
//             'message' => 'Google login successful',
//             'data' => [
//                 'user' => new UserResource($user),
//                 'token' => $token,
//                 'token_type' => 'Bearer',
//             ]
//         ], 200);

//         return $setCorsHeaders($response);

//     } catch (\Exception $e) {
//         // Log the error for debugging
//         \Log::error('Google authentication failed', [
//             'error' => $e->getMessage(),
//             'trace' => $e->getTraceAsString()
//         ]);

//         $response = response()->json([
//             'success' => false,
//             'message' => 'Google authentication failed',
//             'error' => $e->getMessage()
//         ], 500);

//         return $setCorsHeaders($response);
//     }
// }



    public function changePassword(Request $request)
    {
        try {
            // Validate current password and new password
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed', // 'new_password_confirmation' must be sent
            ]);

            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect.'
                ], 422);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'message' => 'Password changed successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Password change failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function microsoftLogin(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $token = $request->token;
            
            $tokenParts = explode(".", $token);
            if (count($tokenParts) !== 3) {
                return response()->json(['error' => 'Invalid token format'], 401);
            }
            
            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);

            if (!$payload) {
                return response()->json(['error' => 'Invalid Microsoft token'], 401);
            }

            // Microsoft token fields (can vary)
            $email = $payload['email'] ?? $payload['preferred_username'] ?? null;
            $name = $payload['name'] ?? $email;
            
            if (!$email) {
                return response()->json(['error' => 'Email not found in Microsoft token'], 401);
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'avatar' => $payload['picture'] ?? '',
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'status' => 'active',
                    'login_type' => 'microsoft',
                ]
            );

            $authToken = $user->createToken(
                'auth_token_' . now()->timestamp,
                ['*'],
                now()->addDays(30)
            )->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Microsoft login successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $authToken,
                    'token_type' => 'Bearer',
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Microsoft login error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Microsoft authentication failed',
                'message' => $e->getMessage(),
            ], 401);
        }
    }
}