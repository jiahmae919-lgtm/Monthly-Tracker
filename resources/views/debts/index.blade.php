<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Debts') }}
            </h2>
            <a href="{{ route('debts.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Debt
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="space-y-6">
                        @forelse($debts as $debt)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $debt->name }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Total: ${{ number_format($debt->total_amount, 2) }} | Monthly: ${{ number_format($debt->monthly_payment, 2) }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $debt->remaining_balance > 0 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                            {{ $debt->remaining_balance > 0 ? 'Active' : 'Paid' }}
                                        </span>
                                        <a href="{{ route('debts.show', $debt) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500">View</a>
                                        <a href="{{ route('debts.edit', $debt) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">Edit</a>
                                        <form method="POST" action="{{ route('debts.destroy', $debt) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                                        <span>Progress</span>
                                        <span>{{ number_format((($debt->total_amount - $debt->remaining_balance) / $debt->total_amount) * 100, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-blue-600 h-3 rounded-full" style="width: {{ (($debt->total_amount - $debt->remaining_balance) / $debt->total_amount) * 100 }}%"></div>
                                    </div>
                                </div>

                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Remaining: ${{ number_format($debt->remaining_balance, 2) }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">No debts found.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $debts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
