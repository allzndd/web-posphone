<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse;
use Laravel\Fortify\Contracts\RequestPasswordResetLinkResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(fn () => view('pages.auth.login'));

        Fortify::registerView(fn () => view('pages.auth.register'));

        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));

        Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));

        // Override Fortify's default RegisterResponse
        // MUST be in boot() to run AFTER Fortify's own register() bindings
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect('/email/verify');
                }
            };
        });

        // Override Fortify's default LoginResponse
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    try {
                        // Check if user is authenticated first (to avoid null pointer error)
                        if (!auth()->check()) {
                            return redirect()->route('login');
                        }

                        $user = auth()->user();

                        // Validate user object
                        if (!$user) {
                            \Log::error('LoginResponse: User object is null after auth check');
                            return redirect()->route('login')->withErrors(['email' => 'Terjadi kesalahan. Silakan login kembali.']);
                        }

                        // Check email verification with fallback
                        try {
                            $isVerified = $user->hasVerifiedEmail();
                        } catch (\Exception $e) {
                            \Log::error('LoginResponse: Error checking email verification', [
                                'user_id' => $user->id,
                                'error' => $e->getMessage()
                            ]);
                            // Fallback: check directly
                            $isVerified = ($user->email_is_verified == 1);
                        }

                        if (!$isVerified) {
                            return redirect()->route('verification.notice');
                        }

                        // Check role and redirect accordingly
                        $roleId = (int)($user->role_id ?? 0);
                        
                        if ($roleId === 1) {
                            // Superadmin
                            return redirect()->route('dashboard-superadmin');
                        }

                        // Owner (role_id = 2) or Admin (role_id = 3) go to regular dashboard
                        return redirect()->intended(route('dashboard'));
                        
                    } catch (\Exception $e) {
                        // Log the error
                        \Log::error('LoginResponse: Unexpected error', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        
                        // Fallback to dashboard
                        return redirect()->route('dashboard');
                    }
                }
            };
        });

        // Override password reset responses
        $this->app->singleton(PasswordResetResponse::class, function () {
            return new class implements PasswordResetResponse {
                public function toResponse($request)
                {
                    return redirect()->route('login')->with('status', 'Your password has been reset.');
                }
            };
        });

        $this->app->singleton(RequestPasswordResetLinkResponse::class, function () {
            return new class implements RequestPasswordResetLinkResponse {
                public function toResponse($request)
                {
                    return back()->with('status', 'We have emailed your password reset link!');
                }
            };
        });
    }
}
