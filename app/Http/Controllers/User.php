<?php

namespace App\Http\Controllers;

use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator ;

class User extends Controller
{
    //
    
    public function register(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'name' => 'required',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => "somethink error",
                    'data' => $validator->errors()
                ], 400);
            }

            $foundUser = ModelsUser::where('email', $request->input('email'))->first();

            if ($foundUser) {
                return response()->json([
                    'message' => 'something error : email already exists',
                    'data' => (object)[]
                ], 400);
            }

            $user = ModelsUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password) 
            ]);

            return response()->json([
                'message' => 'insert success',
                'data' => $user
            ]);

        }catch (\Exception $e) {
            return response()->json([
                'message' => 'somethings error : ' . $e->getMessage(),
                'data' => (object)[]
            ], 500);
        }
    }

    public function login(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'message' => "somethink error",
                    'data' => $validator->errors()
                ], 400);
            }

            $email = $request->input('email');
            $user = ModelsUser::where('email', $email)->first();
    
            if (empty($user)) {
                return response()->json([
                    'message' => 'Email Not Founded',
                    'data' => (object)[]
                ], 400);
            }
    
            if (!Hash::check($request->input('password'), $user->password)) {
                return response()->json([
                    'message' => 'Username or Password is Wrong',
                    'data' => (object)[]
                ], 400);
            }
            $token = $user->createToken($email)->plainTextToken;

            return response()->json([
                'message' => 'login success',
                'data' => $token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'somethings error : ' . $e->getMessage(),
                'data' => (object)[]
            ], 500);
        }
    }

    public function forgotPassword(Request $request) {

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'new_password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => "somethink error",
                    'data' => $validator->errors()
                ], 400);
            }

            $user = ModelsUser::where('email', $request->email)->first();
    
            if (empty($user)) {
                return response()->json([
                    'message' => 'Email Not Founded',
                    'data' => (object)[]
                ], 400);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();
            
            return response()->json([
                'message' => 'forgot password success',
                'data' => $user
            ]);
        }catch(\Exception $e) {
            return response()->json([
                'message' => 'somethings error : ' . $e->getMessage(),
                'data' => (object)[]
            ], 500);
        }
    }

}
