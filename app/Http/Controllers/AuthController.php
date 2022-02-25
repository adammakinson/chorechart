<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AuthController extends Controller
{
    /**
     * login
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);

            $credentials = request(['username', 'password']);

            if (!Auth::attempt($credentials)) {

                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user = User::where('username', $request->username)->first();

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Error logging in');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'status_code' => 200,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'roles' => $user->roles
                ]
            ]);

        } catch (Exception $error) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Error logging in',
                'error' => $error
            ]);
        }
    }

    public function logout(Request $request)
    {
        $loggedInUser = Auth::user();

        if ($loggedInUser) {
            $loggedInUser->tokens()->delete();

            return response()->json([
                'status_code' => 200,
                'message' => 'OK'
            ]);
        } else {
            return response()->json([
                'status_code' => 403,
                'message' => 'Forbidden'
            ]);
        }
    }

    public function register(Request $request)
    {
        
        // Validate the request
        $request->validate([
            'name' => 'string',
            'username' => 'required',
            'password' => 'required',
            'confirm_password' => 'required'
        ]);
            
        if (isset($request->email) && $request->email != "") {
            $request->validate(['email' => 'email']);
        }
        
        $password = $request->password;
        $passwordConfirmation = $request->confirm_password;            
        if ($password !== $passwordConfirmation) {
            throw new \Exception('Your password and password confirmation need to match!');
        }
        
        $userExistsWithUsername = User::withTrashed()->where('username', $request->username)->first();
        $userExistsWithEmail = User::withTrashed()->where('email', $request->email)->first();

        if($userExistsWithUsername != null && $userExistsWithUsername->trashed()) {

            throw new \Exception("The user associated with $request->username was removed. Please contact an admin about restoring it.");
        }
        
        if($userExistsWithEmail != null && $userExistsWithEmail->trashed()) {

            throw new \Exception("The user associated with $request->email was removed. Please contact an admin about restoring it.");
        }

        if ($userExistsWithUsername) {
            throw new \Exception("$request->username already exists. Choose another user name.");
        }
        
        if ($userExistsWithEmail) {
            throw new \Exception("$request->email already exists. Have you registered here before?");
        }
        
        try {
            // Create a new instance of the User model passing in the values
            $user = new User();

            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            // Persist the user model to the database
            $user->save();

            // Give the user the default role
            DB::table('role_user')->insert([
                'role_id' => '2', 'user_id' => $user->id, 'created_at' => now(), 'updated_at' => now()
            ]);

            return response()->json([
                'status_code' => 200,
                'message' => 'Registration successful'
            ]);

        } catch(Exception $error) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Error registering',
                'error' => $error
            ]);
        }
    }

    public function updateUserCredentials(Request $request)
    {
        $loggedInUser = Auth::user();
        
        if (Gate::allows('manage-chorechart') || $request->username === $loggedInUser->username ) {

            // Validate the request
            $request->validate([
                'username' => 'required',
                'password' => 'required|min:8',
                'confirm_password' => 'required|min:8|same:password'
            ]);
    
            $user = User::where('username', $request->username)->first();
            
            try {
                $user->password = Hash::make($request->password);
                $user->save();
    
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Successfully updated credentials'
                ]);
            } catch(Exception $error) {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'Error updating credentials',
                    'error' => $error
                ]);
            }
        }
    }

    public function updateUserInfo(Request $request, $userId)
    {
        $user = User::find($userId);

        $validatedRequest = $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required'
        ]);

        try {
            $user->name = $validatedRequest['name'];
            $user->username = $validatedRequest['username'];
            $user->email = $validatedRequest['email'];

            $user->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'Successfully updated user information'
            ]);
        } catch (Exception $error) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Error updating user information',
                'error' => $error
            ]);
        }
    }
}
