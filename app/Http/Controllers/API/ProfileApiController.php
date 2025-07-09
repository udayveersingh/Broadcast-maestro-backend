<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/auth/profile",
     *     tags={"Profile"},
     *     summary="Get authenticated user's full profile",
     *     description="Returns the authenticated user's basic info and profile details.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile returned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="avatar", type="string", format="url", example="http://example.com/storage/avatars/avatar.jpg"),
     *             @OA\Property(property="role", type="string", example="user"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(
     *                 property="profile",
     *                 type="object",
     *                 @OA\Property(property="phone", type="string", example="+91-9876543210"),
     *                 @OA\Property(property="organization", type="string", example="OpenAI"),
     *                 @OA\Property(property="department", type="string", example="AI Research"),
     *                 @OA\Property(property="country", type="string", example="India"),
     *                 @OA\Property(property="state", type="string", example="Delhi"),
     *                 @OA\Property(property="city", type="string", example="New Delhi"),
     *                 @OA\Property(property="address", type="string", example="123 Street"),
     *                 @OA\Property(property="zip_code", type="string", example="110001"),
     *                 @OA\Property(property="photo_visibility", type="string", example="anyone")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'role' => $user->role,
            'status' => $user->status,
            'profile' => $user->profile, // includes phone, address, etc.
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/profile",
     *     tags={"Profile"},
     *     summary="Update user profile and avatar",
     *     description="Updates profile information and optionally uploads a new avatar image.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="phone", type="string", example="+91-9876543210"),
     *                 @OA\Property(property="organization", type="string", example="OpenAI"),
     *                 @OA\Property(property="department", type="string", example="AI Research"),
     *                 @OA\Property(property="country", type="string", example="India"),
     *                 @OA\Property(property="state", type="string", example="Delhi"),
     *                 @OA\Property(property="city", type="string", example="New Delhi"),
     *                 @OA\Property(property="address", type="string", example="123 Street"),
     *                 @OA\Property(property="zip_code", type="string", example="110001"),
     *                 @OA\Property(property="photo_visibility", type="string", enum={"anyone", "only_me", "connections"}, example="connections"),
     *                 @OA\Property(property="avatar", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="profile", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'phone' => 'nullable|string',
            'organization' => 'nullable|string',
            'department' => 'nullable|string',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'photo_visibility' => 'nullable|in:anyone,only_me,connections',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public'); // stored in storage/app/public/avatars
            $user->avatar = $avatarPath;
            $user->save();
        }

        $profile = $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json(['message' => 'Profile updated successfully', 'profile' => $profile]);
    }

}
