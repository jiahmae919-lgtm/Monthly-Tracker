<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                WELCOME {{ strtoupper(auth()->user()->name) }} THE PALA UTANG
            </h2>

        </div>
    </x-slot>

    <div class="py-10" x-data="monthlyPlannerApp()">
        <div class="max-w-[1800px] mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="flex justify-end">
                <button
                    type="button"
                    @click="openPlannerModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg text-sm shadow-sm"
                >
                    + Add Monthly Plan
                </button>
            </div>

            <div class="grid grid-cols-1 2xl:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                    <div class="p-8 space-y-5 min-h-[360px]">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">MONTHLY REMAINING OVERVIEW</h3>
                            <div class="text-right">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total remaining of all months</p>
                                <p class="text-xl font-bold" :class="subtotalRemaining >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" x-text="formatCurrency(subtotalRemaining)"></p>
                            </div>
                        </div>
                        <div class="rounded-xl p-3 bg-slate-50/60 dark:bg-slate-900/35 border border-slate-200/70 dark:border-slate-700/60">
                            <canvas id="monthlyRemainingChart" height="110"></canvas>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                    <div class="p-8 space-y-5 min-h-[360px]">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">MONTHLY UTANG TRACKER</h3>
                            <span class="text-sm text-gray-500 dark:text-gray-400" x-text="`${filteredPostedMonths().length} result(s)`"></span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wide border-gray-100 text-gray-500 dark:text-gray-400 mb-1.5">Search</label>
                                <input
                                    type="text"
                                    x-model.trim="postedSearch"
                                    @change="currentPage = 1"
                                    placeholder="Search month"
                                    class="h-10 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Remaining Filter</label>
                                <select
                                    x-model="postedFilter"
                                    @change="currentPage = 1"
                                    class="h-10 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="all">All</option>
                                    <option value="positive">Positive only</option>
                                    <option value="negative">Negative only</option>
                                    <option value="zero">Zero only</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Sort</label>
                                <select
                                    x-model="postedSort"
                                    @change="currentPage = 1"
                                    class="h-10 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="latest">Latest first</option>
                                    <option value="oldest">Oldest first</option>
                                    <option value="remaining_desc">Remaining high to low</option>
                                    <option value="remaining_asc">Remaining low to high</option>
                                    <option value="label_asc">Label A-Z</option>
                                    <option value="label_desc">Label Z-A</option>
                                </select>
                            </div>
                        </div>

                        <template x-if="postedMonths.length === 0">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No posted utang yet.</p>
                        </template>
                        <template x-if="postedMonths.length > 0 && filteredPostedMonths().length === 0">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No results match your search/filter.</p>
                        </template>

                        <div class="space-y-4">
                            <template x-for="posted in getPaginatedResults()" :key="posted.id">
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 transition-all hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-600">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100" x-text="posted.label"></h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="'Posted: ' + (posted.created_at ? new Date(posted.created_at).toLocaleString() : '-')"></p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" @click="openEditModal(posted)" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-1.5 px-3 rounded-lg text-xs">
                                                Edit
                                            </button>
                                            <button type="button" @click="deletePostedById(posted.id)" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-4 text-sm">
                                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700/70">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Sahod</p>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100" x-text="formatCurrency(posted.salary)"></p>
                                        </div>
                                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700/70">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Cash</p>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100" x-text="formatCurrency(posted.cash)"></p>
                                        </div>
                                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700/70">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Cash</p>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100" x-text="formatCurrency(posted.total_cash)"></p>
                                        </div>
                                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700/70">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Expenses</p>
                                            <p class="font-semibold text-rose-600 dark:text-rose-400" x-text="formatCurrency(posted.total_expenses)"></p>
                                        </div>
                                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700/70">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Remaining</p>
                                            <p class="font-semibold" :class="posted.remaining >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" x-text="formatCurrency(posted.remaining)"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Cool Pagination -->
                        <template x-if="getTotalPages() > 1">
                            <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="currentPage = Math.max(1, currentPage - 1)"
                                        :disabled="currentPage === 1"
                                        class="inline-flex items-center justify-center h-10 w-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="flex gap-1">
                                        <template x-for="page in getPageNumbers()" :key="page">
                                            <template x-if="page === '...'">
                                                <span class="px-3 h-10 flex items-center text-gray-400 dark:text-gray-500">...</span>
                                            </template>
                                            <template x-if="page !== '...'">
                                                <button
                                                    @click="currentPage = page"
                                                    :class="page === currentPage ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'"
                                                    class="inline-flex items-center justify-center h-10 min-w-[40px] rounded-lg border transition-all font-medium"
                                                    x-text="page"
                                                ></button>
                                            </template>
                                        </template>
                                    </div>
                                    <button
                                        @click="currentPage = Math.min(getTotalPages(), currentPage + 1)"
                                        :disabled="currentPage === getTotalPages()"
                                        class="inline-flex items-center justify-center h-10 w-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="(currentPage - 1) * itemsPerPage + 1"></span>
                                    <span class="mx-1">-</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="Math.min(currentPage * itemsPerPage, filteredPostedMonths().length)"></span>
                                    <span class="mx-1">of</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="filteredPostedMonths().length"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-600 dark:text-gray-400">Per page:</label>
                                    <select
                                        x-model.number="itemsPerPage"
                                        @change="currentPage = 1"
                                        class="h-10 px-3 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm border shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        <option value="3">3</option>
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Planner Modal (Glass Design) -->
        <div
            x-show="showPlannerModal"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm"
            @click="showPlannerModal = false"
        ></div>
        <div
            x-show="showPlannerModal"
            x-transition
            class="fixed inset-0 z-50 overflow-y-auto p-4 md:p-8"
        >
            <div class="min-h-full flex items-start justify-center">
                <div
                    class="w-full max-w-5xl rounded-2xl border border-white/25 bg-white/20 dark:bg-slate-900/35 backdrop-blur-xl shadow-2xl"
                    @click.stop
                >
                    <div class="p-5 md:p-6 space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Planner Input</h3>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Utang now iyak later.</p>
                            </div>
                            <button type="button" @click="showPlannerModal = false" class="h-9 w-9 rounded-full bg-white/50 hover:bg-white/70 dark:bg-slate-700/70 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-100 text-lg leading-none">
                                &times;
                            </button>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" @click="addMonth()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                                + Add Month Note
                            </button>
                        </div>

                        <template x-if="months.length === 0">
                            <div class="text-sm text-gray-700 dark:text-gray-200 border border-white/30 rounded-lg p-4 bg-white/30 dark:bg-slate-800/40">
                                Start by clicking <span class="font-semibold">Add Month Note</span>.
                            </div>
                        </template>

                        <div class="space-y-6">
                            <template x-for="(month, monthIndex) in months" :key="month.id">
                                <div class="border border-white/30 rounded-xl p-4 md:p-5 bg-white/35 dark:bg-slate-800/45 space-y-4">
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                                        <div class="lg:col-span-4">
                                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Month Label</label>
                                            <input type="text" x-model="month.label" placeholder="MONTH" class="h-11 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div class="lg:col-span-3">
                                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Sahod</label>
                                            <input type="number" step="0.01" min="0" x-model.number="month.salary" class="h-11 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div class="lg:col-span-3">
                                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Cash on Hand</label>
                                            <input type="number" step="0.01" x-model.number="month.cash" class="h-11 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div class="lg:col-span-2">
                                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Actions</label>
                                            <div class="flex gap-2">
                                                <button type="button" @click="postMonth(monthIndex)" class="flex-1 h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-sm">Post</button>
                                                <button type="button" @click="removeMonth(monthIndex)" class="flex-1 h-11 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg text-sm">Remove</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Expense Lines</h4>
                                            <button type="button" @click="addExpense(monthIndex)" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2 px-3 rounded-lg text-xs">
                                                + Add Expense
                                            </button>
                                        </div>

                                        <template x-for="(expense, expenseIndex) in month.expenses" :key="expense.id">
                                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
                                                <div class="lg:col-span-7">
                                                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Name</label>
                                                    <input type="text" x-model="expense.label" placeholder="BPI / Motor / Rent / Atome" class="h-10 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                </div>
                                                <div class="lg:col-span-4">
                                                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Amount</label>
                                                    <input type="number" step="0.01" x-model.number="expense.amount" placeholder="0.00" class="h-10 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                </div>
                                                <div class="lg:col-span-1">
                                                    <button type="button" @click="removeExpense(monthIndex, expenseIndex)" class="w-full h-10 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-semibold rounded-lg text-sm">
                                                        X
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                            <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">Total Expenses</p>
                                            <p class="mt-1 text-xl font-bold text-rose-600 dark:text-rose-400" x-text="formatCurrency(totalExpenses(month))"></p>
                                        </div>
                                        <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                            <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">Total Cash (Cash + Sahod)</p>
                                            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100" x-text="formatCurrency(totalCash(month))"></p>
                                        </div>
                                        <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                            <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">Remaining</p>
                                            <p class="mt-1 text-xl font-bold" :class="remaining(month) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" x-text="formatCurrency(remaining(month))"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Posted Modal -->
        <div
            x-show="showEditModal"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm"
            @click="closeEditModal()"
        ></div>
        <div
            x-show="showEditModal"
            x-transition
            class="fixed inset-0 z-50 overflow-y-auto p-4 md:p-8"
        >
            <div class="min-h-full flex items-start justify-center">
                <div class="w-full max-w-4xl rounded-2xl border border-white/25 bg-white/20 dark:bg-slate-900/35 backdrop-blur-xl shadow-2xl" @click.stop>
                    <div class="p-5 md:p-6 space-y-5" x-show="editingEntry">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Posted Utang</h3>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Update and save permanently.</p>
                            </div>
                            <button type="button" @click="closeEditModal()" class="h-9 w-9 rounded-full bg-white/50 hover:bg-white/70 dark:bg-slate-700/70 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-100 text-lg leading-none">&times;</button>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Month Label</label>
                                <input type="text" x-model="editingEntry.label" class="h-11 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Sahod</label>
                                <input type="number" step="0.01" x-model.number="editingEntry.salary" class="h-11 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Cash on Hand</label>
                                <input type="number" step="0.01" x-model.number="editingEntry.cash" class="h-11 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Expense Lines</h4>
                                <button type="button" @click="addEditExpense()" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2 px-3 rounded-lg text-xs">+ Add Expense</button>
                            </div>
                            <template x-for="(expense, expenseIndex) in editingEntry.expenses" :key="expense.id">
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
                                    <div class="lg:col-span-7">
                                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Name</label>
                                        <input type="text" x-model="expense.label" class="h-10 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="lg:col-span-4">
                                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Amount</label>
                                        <input type="number" step="0.01" x-model.number="expense.amount" class="h-10 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="lg:col-span-1">
                                        <button type="button" @click="removeEditExpense(expenseIndex)" class="w-full h-10 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-semibold rounded-lg text-sm">X</button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">Total Expenses</p>
                                <p class="mt-1 text-xl font-bold text-rose-600 dark:text-rose-400" x-text="formatCurrency(editTotalExpenses())"></p>
                            </div>
                            <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">Total Cash</p>
                                <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100" x-text="formatCurrency(editTotalCash())"></p>
                            </div>
                            <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">Remaining</p>
                                <p class="mt-1 text-xl font-bold" :class="editRemaining() >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" x-text="formatCurrency(editRemaining())"></p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="closeEditModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">Cancel</button>
                            <button type="button" @click="saveEdit()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   {{-- all the js is here --}}
    @include('components.js-planner')
</x-app-layout>
