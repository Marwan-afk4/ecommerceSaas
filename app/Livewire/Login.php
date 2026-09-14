<?php

namespace App\Livewire;

use App\Models\ShopApplication;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Login extends Component
{
    #[Locked]
    public bool $superadmin = false;

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function mount(bool $superadmin = false): void
    {
        $this->superadmin = $superadmin || request()->routeIs('superadmin.login');
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            if (
                ! $this->superadmin
                && ShopApplication::query()->pending()->where('admin_email', $this->email)->exists()
            ) {
                throw ValidationException::withMessages([
                    'email' => __('Your store request is waiting for superadmin approval.'),
                ]);
            }

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $user = Auth::user();

        if ($this->superadmin && ! $user->is_platform_admin) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        if (! $this->superadmin && ! $user->shops()->active()->exists()) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        session()->regenerate();

        $this->redirect(
            $this->superadmin ? route('superadmin.dashboard') : route('shop.dashboard'),
            navigate: true,
        );
    }

    public function render()
    {
        return view('livewire.login')
            ->title($this->superadmin ? 'Superadmin' : 'Shop login');
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('Too many login attempts. Please try again in :seconds seconds.', [
                'seconds' => $seconds,
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip().'|'.($this->superadmin ? 'superadmin' : 'shop'));
    }
}
