<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center space-y-2">
            <img
                src="{{ asset('img/logo.png') }}"
                alt="{{ config('app.name', 'CashFlow') }} Logo"
                class="w-28 h-28 mx-auto object-contain"
            >
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Forgot password?') }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                {{ __('Enter the email you use for your account. We will send you a link to choose a new password.') }}
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full h-11"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center h-11">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </form>

        @if (Route::has('login'))
            <div class="rounded-lg border border-indigo-200 bg-indigo-50/70 dark:bg-indigo-900/20 dark:border-indigo-800 p-4">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    {{ __('Remember your password?') }}
                </p>
                <a
                    href="{{ route('login') }}"
                    class="mt-3 inline-flex items-center justify-center w-full h-10 rounded-md bg-white dark:bg-gray-800 border border-indigo-200 dark:border-indigo-700 text-indigo-700 dark:text-indigo-300 font-semibold text-sm hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition"
                >
                    {{ __('Back to log in') }}
                </a>
            </div>
        @endif
    </div>
</x-guest-layout>
