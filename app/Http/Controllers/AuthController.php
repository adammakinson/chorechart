<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
                
                // return response('Invalid credentials', 401);

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
                    'name' => $user->name,
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

    public function register(Request $request)
    {
        try {
            
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

            // Create a new instance of the User model passing in the values
            $user = new User();

            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            // Persist the user model to the database
            $user->save();

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
}
