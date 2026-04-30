<x-app-layout>
    <x-slot name="header">
        @php
            $avatarPath = auth()->user()->gender === 'female' ? asset('img/female.png') : asset('img/male.png');
        @endphp

        <div x-data="{ showUserCard: false }" class="flex items-start justify-between gap-4 px-1 py-2 lg:items-center">
            <div class="flex min-w-0 items-start gap-4">
                <img src="{{ asset('img/logo.png') }}" alt="Logo"
                    class="h-14 w-14 shrink-0 rounded-full border border-slate-200 bg-white p-1 object-contain shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:h-20 sm:w-20" />

                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-300">
                        Utang Dashboard</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900 dark:text-slate-100">
                        Welcome back, {{ auth()->user()->name }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Track your monthly balance, expenses, and due dates in one place.
                    </p>
                </div>
            </div>

            <div class="relative self-start lg:self-auto" @click.outside="showUserCard = false">
                <button type="button" @click="showUserCard = !showUserCard"
                    class="inline-flex items-center gap-3 rounded-full border border-slate-200/70 bg-white/70 p-1.5 shadow-sm transition hover:bg-white dark:border-slate-700/70 dark:bg-slate-900/45 dark:hover:bg-slate-900/70 sm:rounded-2xl sm:px-4 sm:py-3">
                    <img src="{{ $avatarPath }}" alt="Profile avatar"
                        class="h-12 w-12 shrink-0 rounded-full border-2 border-indigo-500 object-cover dark:border-indigo-400">
                    <div class="hidden min-w-0 text-right sm:block">
                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Profile</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Account menu</p>
                    </div>
                    <svg class="hidden h-4 w-4 text-slate-400 transition-transform sm:block"
                        :class="showUserCard ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <div x-cloak x-show="showUserCard" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                    class="absolute right-0 z-30 mt-3 w-[22rem] max-w-[calc(100vw-2rem)] rounded-2xl border border-slate-200/70 bg-white/95 p-4 shadow-xl backdrop-blur dark:border-slate-700/70 dark:bg-slate-900/95">
                    <div class="flex items-center gap-3">
                        <img src="{{ $avatarPath }}" alt="Profile avatar"
                            class="h-14 w-14 shrink-0 rounded-full border-2 border-indigo-500 object-cover dark:border-indigo-400">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-base font-semibold text-slate-900 dark:text-slate-100">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full border border-indigo-200/70 bg-white/70 px-2.5 py-1 text-[11px] font-semibold text-indigo-700 dark:border-indigo-500/20 dark:bg-slate-900/50 dark:text-indigo-300">
                            Account
                        </span>
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <a href="{{ route('profile.edit') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <style>
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="py-10" x-data="monthlyPlannerApp()">
        <div class="max-w-[1800px] mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Top bar: summary cards aligned with the Add Monthly Plan button -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                <!-- Compact Upcoming / Overdue cards -->
                <div class="grid grid-cols-2 sm:flex sm:items-center gap-3">
                    <button type="button" @click="dueModalType = 'upcoming'; showDueModal = true"
                        class="w-full sm:w-56 rounded-2xl border border-emerald-200/70 bg-emerald-50/60 p-3 text-left shadow-sm transition hover:bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/15">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p
                                    class="text-[10px] leading-tight font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">
                                    UPCOMING DUE DATES</p>
                                <p class="mt-1 text-sm font-bold text-emerald-700 dark:text-emerald-300"
                                    x-text="formatCurrency(upcomingDueExpenses().reduce((sum, e) => sum + (Number(e.amount) || 0), 0))">
                                </p>
                                <p class="mt-0.5 text-[10px] text-gray-600 dark:text-gray-400"
                                    x-text="`${upcomingDueExpenses().length} item(s)`"></p>
                            </div>
                            <div
                                class="h-8 w-8 shrink-0 rounded-xl bg-white/50 dark:bg-slate-900/50 flex items-center justify-center">
                                <svg class="h-4 w-4 text-emerald-700 dark:text-emerald-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <button type="button" @click="dueModalType = 'overdue'; showDueModal = true"
                        class="w-full sm:w-56 rounded-2xl border border-rose-200/70 bg-rose-50/60 p-3 text-left shadow-sm transition hover:bg-rose-50 dark:border-rose-500/20 dark:bg-rose-500/10 dark:hover:bg-rose-500/15">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p
                                    class="text-[10px] leading-tight font-semibold uppercase tracking-wide text-rose-700 dark:text-rose-300">
                                    OVERDUE EXPENSES</p>
                                <p class="mt-1 text-sm font-bold text-rose-700 dark:text-rose-300"
                                    x-text="formatCurrency(overdueExpenses().reduce((sum, e) => sum + (Number(e.amount) || 0), 0))">
                                </p>
                                <p class="mt-0.5 text-[10px] text-gray-600 dark:text-gray-400"
                                    x-text="`${overdueExpenses().length} item(s)`"></p>
                            </div>
                            <div
                                class="h-8 w-8 shrink-0 rounded-xl bg-white/50 dark:bg-slate-900/50 flex items-center justify-center">
                                <svg class="h-4 w-4 text-rose-700 dark:text-rose-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.25">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01M10.29 3.86l-7.4 12.82A2 2 0 004.61 19h14.78a2 2 0 001.72-2.32l-7.4-12.82a2 2 0 00-3.44 0z" />
                                </svg>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Add Monthly Plan button -->
                <button type="button" @click="openPlannerModal()"
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg text-sm shadow-sm">
                    + Add Monthly Plan
                </button>
            </div>

            <div class="overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-5">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-8">
                        <div class="space-y-4 md:pr-6 ">

                            <div class="hidden items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">UPCOMING DUE
                                        DATES</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Unpaid expenses due within the
                                        next 30 days.</p>
                                </div>
                                <span
                                    class="hidden sm:inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300"
                                    x-text="`${upcomingDueExpenses().length} item(s)`"></span>
                            </div>

                            <template x-if="false && upcomingDueExpenses().length === 0">
                                <div
                                    class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-3 text-sm text-gray-500 dark:text-gray-400">
                                    No upcoming unpaid expenses.
                                </div>
                            </template>

                            <div x-show="false && upcomingDueExpenses().length > 0" x-data="{ page: 0, pageSize: 3 }"
                                x-effect="
                                    (() => {
                                        const totalPages = Math.max(1, Math.ceil(upcomingDueExpenses().length / pageSize));
                                        page = Math.min(page, totalPages - 1);
                                    })()
                                "
                                class="space-y-3">
                                <template
                                    x-for="(expense, expenseIndex) in upcomingDueExpenses().slice(page * pageSize, (page + 1) * pageSize)"
                                    :key="`upcoming-top-${expense.monthId}-${expense.expenseId ?? expenseIndex}`">
                                    <div
                                        class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-slate-50/70 dark:bg-slate-900/35 px-3 py-2.5">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100"
                                                    x-text="expense.label || 'Unnamed expense'"></p>
                                                <span
                                                    class="hidden sm:inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                                                    Due Soon
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"
                                                x-text="`${expense.monthLabel} â€¢ ${formatPlannerDateOnly(expense.due_date)}`">
                                            </p>
                                        </div>
                                        <div class="shrink-0 text-right">
                                            <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-300"
                                                x-text="formatCurrency(expense.amount)"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"
                                                x-text="formatDaysUntil(expense.due_date)"></p>
                                        </div>
                                    </div>
                                </template>

                                <div class="flex items-center justify-end gap-2 pt-1"
                                    x-show="upcomingDueExpenses().length > pageSize">
                                    <span class="hidden sm:inline mr-auto text-xs text-gray-500 dark:text-gray-400"
                                        x-text="`Page ${page + 1} of ${Math.max(1, Math.ceil(upcomingDueExpenses().length / pageSize))}`"></span>

                                    <button type="button"
                                        class="inline-flex h-8 items-center justify-center rounded-md border border-gray-300 px-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                                        @click="page = Math.max(0, page - 1)" :disabled="page === 0">
                                        Prev
                                    </button>

                                    <button type="button"
                                        class="inline-flex h-8 items-center justify-center rounded-md border border-gray-300 px-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                                        @click="page = Math.min(Math.max(0, Math.ceil(upcomingDueExpenses().length / pageSize) - 1), page + 1)"
                                        :disabled="page >= Math.ceil(upcomingDueExpenses().length / pageSize) - 1">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 hidden">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">OVERDUE
                                        EXPENSES</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Unpaid expenses past their due
                                        date.</p>
                                </div>
                                <span
                                    class="hidden sm:inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/15 dark:text-rose-300"
                                    x-text="`${overdueExpenses().length} item(s)`"></span>
                            </div>

                            <template x-if="overdueExpenses().length === 0">
                                <div
                                    class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-3 text-sm text-gray-500 dark:text-gray-400">
                                    No overdue expenses.
                                </div>
                            </template>

                            <div x-show="overdueExpenses().length > 0" x-data="{ page: 0, pageSize: 3 }"
                                x-effect="
                                    (() => {
                                        const totalPages = Math.max(1, Math.ceil(overdueExpenses().length / pageSize));
                                        page = Math.min(page, totalPages - 1);
                                    })()
                                "
                                class="space-y-3">
                                <template
                                    x-for="(expense, expenseIndex) in overdueExpenses().slice(page * pageSize, (page + 1) * pageSize)"
                                    :key="`overdue-top-${expense.monthId}-${expense.expenseId ?? expenseIndex}`">
                                    <div
                                        class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-slate-50/70 dark:bg-slate-900/35 px-3 py-2.5">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100"
                                                    x-text="expense.label || 'Unnamed expense'"></p>
                                                <span
                                                    class="hidden sm:inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-semibold text-rose-700 dark:bg-rose-500/15 dark:text-rose-300">
                                                    Overdue
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"
                                                x-text="`${expense.monthLabel} â€¢ ${formatPlannerDateOnly(expense.due_date)}`">
                                            </p>
                                        </div>
                                        <div class="shrink-0 text-right">
                                            <p class="text-sm font-semibold text-rose-600 dark:text-rose-300"
                                                x-text="formatCurrency(expense.amount)"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"
                                                x-text="formatOverdueText(expense.due_date)"></p>
                                        </div>
                                    </div>
                                </template>

                                <div class="flex items-center justify-end gap-2 pt-1"
                                    x-show="overdueExpenses().length > pageSize">
                                    <span class="hidden sm:inline mr-auto text-xs text-gray-500 dark:text-gray-400"
                                        x-text="`Page ${page + 1} of ${Math.max(1, Math.ceil(overdueExpenses().length / pageSize))}`"></span>

                                    <button type="button"
                                        class="inline-flex h-8 items-center justify-center rounded-md border border-gray-300 px-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                                        @click="page = Math.max(0, page - 1)" :disabled="page === 0">
                                        Prev
                                    </button>

                                    <button type="button"
                                        class="inline-flex h-8 items-center justify-center rounded-md border border-gray-300 px-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                                        @click="page = Math.min(Math.max(0, Math.ceil(overdueExpenses().length / pageSize) - 1), page + 1)"
                                        :disabled="page >= Math.ceil(overdueExpenses().length / pageSize) - 1">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 2xl:grid-cols-2 gap-8">
                <div class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="p-8 space-y-5 min-h-[420px]">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">MONTHLY BALANCE
                                    OVERVIEW</h3>
                                <div class="text-right">
                                    <p
                                        class="hidden sm:block text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        Total
                                        balance across all months</p>
                                    <p class="text-xl font-bold"
                                        :class="subtotalRemaining >= 0 ? 'text-emerald-600 dark:text-emerald-400' :
                                            'text-rose-600 dark:text-rose-400'"
                                        x-text="formatCurrency(subtotalRemaining)"></p>
                                </div>
                            </div>
                            <div
                                class="relative w-full h-64 sm:h-72 md:h-80 min-h-[16rem] rounded-xl p-3 bg-slate-50/60 dark:bg-slate-900/35 border border-slate-200/70 dark:border-slate-700/60">
                                <canvas id="monthlyRemainingChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="p-8 space-y-5">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <h4
                                        class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        <span
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12.75 11.25 15 15 9.75M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </span>
                                        <span>Paid Expenses by Month</span>
                                    </h4>
                                    <span class="hidden sm:inline text-xs text-gray-500 dark:text-gray-400"
                                        x-text="`${postedMonths.filter(posted => (posted.expenses || []).some(expense => expense.paid)).length} month(s)`"></span>
                                </div>

                                <template
                                    x-if="postedMonths.filter(posted => (posted.expenses || []).some(expense => expense.paid)).length === 0">
                                    <div
                                        class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-4 text-sm text-gray-500 dark:text-gray-400">
                                        No paid expenses yet.
                                    </div>
                                </template>

                                <div x-show="postedMonths.filter(posted => (posted.expenses || []).some(expense => expense.paid)).length > 0"
                                    :class="postedMonths.filter(posted => (posted.expenses || []).some(expense => expense.paid))
                                        .length > 4 ? 'max-h-[34rem] overflow-y-auto pr-1 hide-scrollbar' : ''"
                                    class="space-y-4">
                                    <template
                                        x-for="posted in postedMonths.filter(posted => (posted.expenses || []).some(expense => expense.paid))"
                                        :key="`paid-expenses-${posted.id}`">
                                        <div
                                            class="rounded-xl border border-gray-200 dark:border-gray-700 bg-slate-50/70 dark:bg-slate-900/35 p-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <button type="button" @click="toggleExpanded(`paid-${posted.id}`)"
                                                    class="flex min-w-0 flex-1 items-start gap-3 text-left">
                                                    <span
                                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5"
                                                            aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 12.75 11.25 15 15 9.75M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z" />
                                                        </svg>
                                                    </span>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center gap-2">
                                                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100"
                                                                x-text="`${posted.label}${posted.year ? ' ' + posted.year : ''}`">
                                                            </p>
                                                            <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform"
                                                                :class="expandedEntries.has(`paid-${posted.id}`) ?
                                                                    'rotate-180' : ''"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor" stroke-width="2"
                                                                aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                            </svg>
                                                        </div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Click the
                                                            month
                                                            to view paid expenses</p>
                                                    </div>
                                                </button>
                                                <div class="flex flex-col items-end gap-2">
                                                    <span
                                                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5"
                                                            aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span
                                                            x-text="`${(posted.expenses || []).filter(expense => expense.paid).length} paid`"></span>
                                                    </span>
                                                    <p class="text-right">
                                                        <span
                                                            class="block text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Total
                                                            Paid</span>
                                                        <span
                                                            class="text-sm font-semibold text-emerald-600 dark:text-emerald-400"
                                                            x-text="formatCurrency(totalPaidExpenses(posted))"></span>
                                                    </p>
                                                </div>
                                            </div>

                                            <div x-show="expandedEntries.has(`paid-${posted.id}`)"
                                                class="mt-4 space-y-2">
                                                <div class="space-y-2">
                                                    <template
                                                        x-for="(expense, expenseIndex) in (posted.expenses || []).filter(expense => expense.paid)"
                                                        :key="`${posted.id}-paid-${expense.id ?? expenseIndex}`">
                                                        <div
                                                            class="flex items-center gap-3 rounded-lg bg-white/80 dark:bg-slate-800/60 px-3 py-2">
                                                            <span
                                                                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300">
                                                                <svg class="h-4 w-4" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2.5" aria-hidden="true">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </span>
                                                            <div class="min-w-0 flex-1">
                                                                <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-100"
                                                                    x-text="expense.label || 'Unnamed expense'"></p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400"
                                                                    x-text="expense.due_date ? formatPlannerDateOnly(expense.due_date) : 'No due date'">
                                                                </p>
                                                            </div>
                                                            <p class="shrink-0 text-sm font-semibold text-emerald-600 dark:text-emerald-400"
                                                                x-text="formatCurrency(expense.amount)"></p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                    <div class="p-8 space-y-5 min-h-[360px]">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">MONTHLY DEBT TRACKER
                            </h3>
                            <span class="hidden sm:inline text-sm text-gray-500 dark:text-gray-400"
                                x-text="`${filteredPostedMonths().length} result(s)`"></span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold uppercase tracking-wide border-gray-100 text-gray-500 dark:text-gray-400 mb-1.5">Search</label>
                                <input type="text" x-model.trim="postedSearch" @change="currentPage = 1"
                                    placeholder=" Search by month"
                                    class="h-10 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Balance
                                    Filter</label>
                                <select x-model="postedFilter" @change="currentPage = 1"
                                    class="h-10 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="all">All</option>
                                    <option value="positive">Positive only</option>
                                    <option value="negative">Negative only</option>
                                    <option value="zero">Zero only</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Sort</label>
                                <select x-model="postedSort" @change="currentPage = 1"
                                    class="h-10 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="latest">Newest first</option>
                                    <option value="oldest">Oldest first</option>
                                    <option value="remaining_desc">Highest balance first</option>
                                    <option value="remaining_asc">Lowest balance first</option>
                                    <option value="label_asc">Month A-Z</option>
                                    <option value="label_desc">Month Z-A</option>
                                </select>
                            </div>
                        </div>

                        <template x-if="postedMonths.length === 0">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No saved debt records yet.</p>
                        </template>
                        <template x-if="postedMonths.length > 0 && filteredPostedMonths().length === 0">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No results match your search or filter.
                            </p>
                        </template>

                        <div class="space-y-4">
                            <template x-for="posted in getPaginatedResults()" :key="posted.id">
                                <div x-data="{ showActions: false }"
                                    class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 transition-all hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-600">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100"
                                                x-text="`${posted.label}${posted.year ? ' ' + posted.year : ''}`"></h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                                x-text="'Saved: ' + (posted.created_at ? new Date(posted.created_at).toLocaleDateString('en-US', {timeZone: 'Asia/Manila', dateStyle: 'medium'}) : '-')">
                                            </p>
                                        </div>
                                        <div class="relative" @click.outside="showActions = false">
                                            <button type="button" @click="showActions = !showActions"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400/60 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800 md:hidden"
                                                aria-label="Open actions">
                                                <svg class="h-4 w-4 transition-transform"
                                                    :class="showActions ? 'rotate-180' : ''" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>

                                            <div x-cloak x-show="showActions" x-transition
                                                class="absolute right-0 z-20 mt-2 w-40 rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-800 md:hidden">
                                                <button type="button"
                                                    @click="openViewModal(posted); showActions = false"
                                                    class="mb-1 inline-flex w-full items-center justify-center rounded-md border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200 dark:hover:bg-emerald-500/20">
                                                    View
                                                </button>
                                                <button type="button"
                                                    @click="openEditModal(posted); showActions = false"
                                                    class="mb-1 inline-flex w-full items-center justify-center rounded-md border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-100 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-200 dark:hover:bg-sky-500/20">
                                                    Edit
                                                </button>
                                                <button type="button"
                                                    @click="deletePostedById(posted.id); showActions = false"
                                                    class="inline-flex w-full items-center justify-center rounded-md border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200 dark:hover:bg-rose-500/20">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>

                                        <div class="hidden flex-wrap gap-2 md:flex">
                                            <button type="button" @click="openViewModal(posted)"
                                                class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-400/60 focus:ring-offset-2 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200 dark:hover:bg-emerald-500/20 dark:focus:ring-offset-gray-800">
                                                View
                                            </button>
                                            <button type="button" @click="openEditModal(posted)"
                                                class="inline-flex items-center justify-center rounded-lg border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-400/60 focus:ring-offset-2 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-200 dark:hover:bg-sky-500/20 dark:focus:ring-offset-gray-800">
                                                Edit
                                            </button>
                                            <button type="button" @click="deletePostedById(posted.id)"
                                                class="inline-flex items-center justify-center rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-rose-400/60 focus:ring-offset-2 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200 dark:hover:bg-rose-500/20 dark:focus:ring-offset-gray-800">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4 text-sm">
                                        <div class="hidden md:block p-3 rounded bg-gray-50 dark:bg-gray-700/70">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Sahod</p>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100"
                                                x-text="formatCurrency(posted.salary)"></p>
                                        </div>
                                        <div class="hidden md:block p-3 rounded bg-gray-50 dark:bg-gray-700/70">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Cash</p>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100"
                                                x-text="formatCurrency(posted.cash)"></p>
                                        </div>
                                        <div class="hidden md:block p-3 rounded bg-gray-50 dark:bg-gray-700/70">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Cash</p>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100"
                                                x-text="formatCurrency(posted.total_cash)"></p>
                                        </div>
                                        <div
                                            class="col-span-2 md:col-span-1 p-3 rounded bg-gray-50 dark:bg-gray-700/70">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Expenses</p>
                                            <p class="font-semibold text-rose-600 dark:text-rose-400"
                                                x-text="formatCurrency(posted.total_expenses)"></p>
                                        </div>
                                        <div
                                            class="col-span-2 md:col-span-1 p-3 rounded bg-gray-50 dark:bg-gray-700/70">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Balance</p>
                                            <p class="font-semibold"
                                                :class="posted.remaining >= 0 ? 'text-emerald-600 dark:text-emerald-400' :
                                                    'text-rose-600 dark:text-rose-400'"
                                                x-text="formatCurrency(posted.remaining)"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Cool Pagination -->
                        <template x-if="getTotalPages() > 1">
                            <div
                                class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                                <div class="flex items-center gap-2">
                                    <button @click="currentPage = Math.max(1, currentPage - 1)"
                                        :disabled="currentPage === 1"
                                        class="inline-flex items-center justify-center h-10 w-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="flex gap-1">
                                        <template x-for="page in getPageNumbers()" :key="page">
                                            <template x-if="page === '...'">
                                                <span
                                                    class="px-3 h-10 flex items-center text-gray-400 dark:text-gray-500">...</span>
                                            </template>
                                            <template x-if="page !== '...'">
                                                <button @click="currentPage = page"
                                                    :class="page === currentPage ?
                                                        'bg-indigo-600 text-white border-indigo-600' :
                                                        'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'"
                                                    class="inline-flex items-center justify-center h-10 min-w-[40px] rounded-lg border transition-all font-medium"
                                                    x-text="page"></button>
                                            </template>
                                        </template>
                                    </div>
                                    <button @click="currentPage = Math.min(getTotalPages(), currentPage + 1)"
                                        :disabled="currentPage === getTotalPages()"
                                        class="inline-flex items-center justify-center h-10 w-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100"
                                        x-text="(currentPage - 1) * itemsPerPage + 1"></span>
                                    <span class="mx-1">-</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100"
                                        x-text="Math.min(currentPage * itemsPerPage, filteredPostedMonths().length)"></span>
                                    <span class="mx-1">of</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100"
                                        x-text="filteredPostedMonths().length"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-600 dark:text-gray-400">Per page:</label>
                                    <select x-model.number="itemsPerPage" @change="currentPage = 1"
                                        class="h-10 px-3 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm border shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
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

        <!-- Modals -->
        @include('components.modals.planner')
        @include('components.modals.edit')
        @include('components.modals.view')
        @include('components.modals.due')
    </div>

    {{-- all the js is here --}}
    @include('components.js-planner')
</x-app-layout>
