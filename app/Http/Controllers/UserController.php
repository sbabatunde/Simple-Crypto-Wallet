<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function createNewUser(CreateUserRequest $request)
    {
        // Validation passed, create and store the user
        $user = User::create([
            'phone' => $request->input('phone'),
            'password' => bcrypt($request->input('password')),
        ]);
        // return user details
        return response()->json($user);
    }

    public function login(Request $request)
    {
        //To validate the login form requests from a user
        validator($request->all(), [
            'phone' => 'required|numeric|min:10',
            'password' => 'required',
        ])->validate();

        //get first record corresponding to the inputted phone number
        $user = User::where('phone', request('phone'))->first();
        //check if the user password in the database and that from the form is the same
        //if it matches then create a token
        if (Hash::check(request('password'), $user->getAuthPassword())) {
            return [
                'token' => $user->createToken(time())->plainTextToken
            ];
        }
        return response()->json();
    }

    public function logout()
    {
        //For deleting user's current access token
        auth()->user()->currentAccessToken()->delete();
    }

    public function dashboard()
    {
        return [
            'user' => auth()->user()
        ];
    }
}
