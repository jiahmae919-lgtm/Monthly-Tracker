<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center space-y-2">
            <img
                src="{{ asset('img/logo.png') }}"
                alt="{{ config('app.name', 'CashFlow') }} Logo"
                class="w-28 h-28 mx-auto object-contain"
            >
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Welcome back</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Log in to continue managing your monthly utang tracker.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full h-11" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full h-11"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="Enter your password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <x-primary-button class="w-full justify-center h-11">
                {{ __('Log in') }}
            </x-primary-button>
        </form>

        @if (Route::has('register'))
            <div class="rounded-lg border border-indigo-200 bg-indigo-50/70 dark:bg-indigo-900/20 dark:border-indigo-800 p-4">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    New here? Create an account to start tracking your monthly utang and cash flow.
                </p>
                <a href="{{ route('register') }}" class="mt-3 inline-flex items-center justify-center w-full h-10 rounded-md bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition">
                    Go to Registration
                </a>
            </div>
        @endif
    </div>
</x-guest-layout>
