<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly \App\Domain\Setting\Services\SettingService $settings
    ) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Rate limiting dinâmico
        $maxAttempts = $this->settings->get('login_max_attempts', 5);
        $decayMinutes = $this->settings->get('login_decay_minutes', 1);

        // Chave composta por email e IP para evitar Account Lockout DoS
        // Assim um robô errando senhas não trava o acesso de outros computadores
        $key = 'login.' . $request->input('email') . '|' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Muitas tentativas. Aguarde {$seconds} segundos.",
            ]);
        }

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, $decayMinutes * 60);

            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha incorretos.',
            ]);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Sua conta está desativada. Entre em contato com a agência.',
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($key);
        $user->update(['last_login_at' => now()]);
        $request->session()->regenerate();

        return $user->isAdmin()
            ? redirect()->route('admin.painel')
            : redirect()->route('cliente.painel');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
