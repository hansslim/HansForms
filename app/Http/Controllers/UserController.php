<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed|min:8'
        ]);

        User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return Auth::user();
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect']
        ]);
    }

    public function logout(Request $request) {
        Auth::guard('web')->logout();
    }

    public function show(Request $request) {
        $user = Auth::user();
        if ($user) {
            return $user;
        }
        else return response('Unauthorized', 401);
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $fields = $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:8',
                'new_password_confirmation' => 'required|string|min:8'
            ]);
            
            if (!password_verify($fields['old_password'], $user->password)) {
                return response('Old password is incorrect.', 400);
            }

            if ($fields['old_password'] === $fields['new_password']) {
                return response('New password cannot be same as old password.', 400);
            }
            if ($fields['new_password_confirmation'] !== $fields['new_password']) {
                return response("New password and it's confirmation aren't same.", 400);
            }

            User::find($user->id)->update([
                'password' => bcrypt($fields['new_password'])
            ]);
            return response('Password was changed successfully.', 200);
        }
        else return response('Unauthorized', 401);
    }

    public function destroy(Request $request)
    {
        
    }
}
