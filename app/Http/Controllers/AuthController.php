<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('authentication.login');
    }

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate token
            $token = $user->createToken('API Token')->plainTextToken;

            $tokenCookie = cookie('token', $token, 60);
            $userIdCookie = cookie('user_id', $user->id);

            // Redirect with cookies
            return redirect('/')
                ->with('token', $token)
                ->withCookie($tokenCookie)
                ->withCookie($userIdCookie);
        }
        return redirect()->back()->withErrors(['message' => 'Invalid credentials'], 401);
    }

    public function showRegisterForm()
    {
        return view('authentication.register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $validatedData['user_type'] = 'user';
        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);
        Auth::login($user);
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        Auth::logout();
        return redirect('/');
    }

    public function getPermissions()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        return response()->json([
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    public function authUser()
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
        ]);
    }
}
