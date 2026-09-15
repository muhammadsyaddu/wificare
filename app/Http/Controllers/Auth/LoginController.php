<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const LOCKOUT_SECONDS = 60;


    /**
     * Tampilkan form login WiFiCare.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }


    /**
     * Proses autentikasi login dengan pembatasan percobaan login.
     */
    public function login(
        Request $request,
        RateLimiter $limiter
    ): RedirectResponse {

        $validated = $request->validate(
            [
                'email' => [
                    'required',
                    'email',
                    'max:150'
                ],

                'password' => [
                    'required',
                    'string'
                ],
            ],
            [
                'email.required' =>
                    'Email wajib diisi.',

                'email.email' =>
                    'Format email tidak valid.',

                'password.required' =>
                    'Password wajib diisi.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Normalisasi email
        |--------------------------------------------------------------------------
        */

        $email = Str::lower(
            trim($validated['email'])
        );


        /*
        |--------------------------------------------------------------------------
        | Rate limiting
        |--------------------------------------------------------------------------
        |
        | Key menggunakan kombinasi email + IP.
        | Tujuannya mencegah brute-force login.
        |
        */

        $throttleKey = $this->throttleKey(
            $email,
            $request
        );


        if (
            $limiter->tooManyAttempts(
                $throttleKey,
                self::MAX_LOGIN_ATTEMPTS
            )
        ) {

            $seconds =
                $limiter->availableIn(
                    $throttleKey
                );


            return back()
                ->withInput(
                    $request->only(
                        'email',
                        'remember'
                    )
                )
                ->withErrors([
                    'email' =>
                        'Terlalu banyak percobaan login. ' .
                        'Coba lagi dalam ' .
                        $seconds .
                        ' detik.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        $credentials = [
            'email' => $email,
            'password' => $validated['password'],
        ];


        if (
            ! Auth::attempt(
                $credentials,
                $request->boolean('remember')
            )
        ) {

            $limiter->hit(
                $throttleKey,
                self::LOCKOUT_SECONDS
            );


            return back()
                ->withInput(
                    $request->only(
                        'email',
                        'remember'
                    )
                )
                ->withErrors([
                    'email' =>
                        'Email atau password salah.',
                ]);
        }


        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | Cek status akun
        |--------------------------------------------------------------------------
        */

        if (! $user->is_active) {

            Auth::logout();

            $request
                ->session()
                ->invalidate();

            $request
                ->session()
                ->regenerateToken();


            return back()
                ->withInput(
                    $request->only('email')
                )
                ->withErrors([
                    'email' =>
                        'Akun Anda telah dinonaktifkan. ' .
                        'Hubungi administrator.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Authentication berhasil
        |--------------------------------------------------------------------------
        |
        | Setelah login berhasil, throttle dihapus.
        |
        */

        $limiter->clear(
            $throttleKey
        );


        /*
        |--------------------------------------------------------------------------
        | Session fixation protection
        |--------------------------------------------------------------------------
        */

        $request
            ->session()
            ->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Audit log
        |--------------------------------------------------------------------------
        */

        AuditLog::record(
            'login',
            $user
        );


        return $this->redirectByRole(
            $user
        );
    }


    /**
     * Proses logout.
     */
    public function logout(
        Request $request
    ): RedirectResponse {

        AuditLog::record(
            'logout',
            Auth::user()
        );


        Auth::logout();


        $request
            ->session()
            ->invalidate();


        $request
            ->session()
            ->regenerateToken();


        return redirect()->route('login');
    }


    /**
     * Membuat key rate limiter.
     */
    private function throttleKey(
        string $email,
        Request $request
    ): string {

        return Str::transliterate(
            Str::lower($email)
            . '|'
            . $request->ip()
        );
    }


    /**
     * Redirect user berdasarkan role.
     */
    private function redirectByRole(
        $user
    ): RedirectResponse {

        if ($user->isAdmin()) {

            return redirect()->route(
                'admin.dashboard'
            );
        }


        if ($user->isTechnician()) {

            return redirect()->route(
                'teknisi.dashboard'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Fallback
        |--------------------------------------------------------------------------
        */

        Auth::logout();


        return redirect()
            ->route('login')
            ->withErrors([
                'email' =>
                    'Role akun tidak memiliki akses ke sistem.',
            ]);
    }
}