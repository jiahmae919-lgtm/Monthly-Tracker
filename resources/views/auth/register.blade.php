<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create your account</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Start tracking your monthly utang, cash, and earnings in one place.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full h-11" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Your full name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full h-11" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full h-11"
                                type="password"
                                name="password"
                                required autocomplete="new-password"
                                placeholder="Create a password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input id="password_confirmation" class="block mt-1 w-full h-11"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Confirm your password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center h-11">
                {{ __('Register') }}
            </x-primary-button>
        </form>

        <div class="rounded-lg border border-indigo-200 bg-indigo-50/70 dark:bg-indigo-900/20 dark:border-indigo-800 p-4">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Already have an account? Log in to continue your tracking.
            </p>
            <a href="{{ route('login') }}" class="mt-3 inline-flex items-center justify-center w-full h-10 rounded-md bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition">
                Go to Login
            </a>
        </div>
    </div>
</x-guest-layout>
