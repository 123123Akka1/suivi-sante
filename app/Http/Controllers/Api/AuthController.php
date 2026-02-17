<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // -------------------------------
    // Register
    // -------------------------------
  public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'age' => 'required|integer',
        'weight' => 'required|numeric',
        'height' => 'required|numeric',
        'gender' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $data = $request->only(['name','email','password','age','weight','height','gender']);
    $data['password'] = Hash::make($data['password']);

    // ✅ Upload to Cloudinary
    if ($request->hasFile('image')) {
        $uploadedFile = Cloudinary::upload(
            $request->file('image')->getRealPath(),
            ['folder' => 'profiles']
        );
        $data['image'] = $uploadedFile->getSecurePath(); // URL كامل!
    }

    $user = User::create($data);
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user
    ], 201);
}


    // -------------------------------
    // Login
    // -------------------------------
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // -------------------------------
    // Logout
    // -------------------------------
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
   public function updateProfile(Request $request)
{
    try {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $request->user()->id,
            'age'          => 'required|integer|min:1',
            'weight'       => 'required|numeric|min:1',
            'height'       => 'required|numeric|min:1',
            'gender'       => 'required|in:male,female',
            'image_base64' => 'nullable|string',
        ]);

        $user = $request->user();
        
        $user->update([
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'age'    => $validated['age'],
            'weight' => $validated['weight'],
            'height' => $validated['height'],
            'gender' => $validated['gender'],
        ]);

        // Upload base64 image to Cloudinary
        if (!empty($validated['image_base64'])) {
            Log::info('Uploading base64 image to Cloudinary...');
            
            
            $uploadedFile = Cloudinary::uploadFile($validated['image_base64'], [
                'folder' => 'profiles',
                'resource_type' => 'image',
            ]);
            
            $imageUrl = $uploadedFile->getSecurePath();
            Log::info('Image uploaded: ' . $imageUrl);
            
            $user->image = $imageUrl;
            $user->save();
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user->fresh()
        ]);

    } catch (\Exception $e) {
        Log::error('Update profile error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    // -------------------------------
    // Profile
    // -------------------------------
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
    // For Flutter Dashboard
        public function user(Request $request)
        {
            return response()->json([
                'user' => $request->user()
            ]);
        }

}
