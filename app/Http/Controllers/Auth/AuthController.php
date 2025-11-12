<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = $this->authService->login(data: $request->validated());

            if (!$user) {
                return back()->withErrors([
                    'email' => 'Invalid credentials',
                ])->withInput($request->only('email'));
            }

            return redirect()->intended('/dashboard');
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to login',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function register(Request $request)
    {

    }

    public function passwordReset(Request $request)
    {

    }
}
