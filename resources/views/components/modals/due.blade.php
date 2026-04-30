        <!-- Due Modal (Upcoming or Overdue) -->
        <div x-show="showDueModal" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm"
            @click="showDueModal = false"></div>
        <div x-show="showDueModal" x-transition class="fixed inset-0 z-50 overflow-y-auto p-4 md:p-8">
            <div class="min-h-full flex items-start justify-center">
                <div class="w-full max-w-2xl rounded-2xl border border-white/25 bg-white/20 dark:bg-slate-900/35 backdrop-blur-xl shadow-2xl"
                    @click.stop>
                    <div class="p-5 md:p-6 space-y-5">
                        <div class="flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold"
                                    :class="dueModalType === 'upcoming' ? 'text-emerald-700 dark:text-emerald-300' :
                                        'text-rose-700 dark:text-rose-300'"
                                    x-text="dueModalType === 'upcoming' ? 'UPCOMING DUE DATES' : 'OVERDUE EXPENSES'">
                                </h3>
                                <p class="text-sm text-gray-700 dark:text-gray-300"
                                    x-text="dueModalType === 'upcoming' ? 'Unpaid expenses due within the next 30 days.' : 'Unpaid expenses past their due date.'">
                                </p>
                            </div>
                            <button type="button" @click="showDueModal = false"
                                class="h-9 w-9 rounded-full bg-white/50 hover:bg-white/70 dark:bg-slate-700/70 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-100 text-lg leading-none">
                                &times;
                            </button>
                        </div>

                        <!-- Upcoming Due Dates list -->
                        <div x-show="dueModalType === 'upcoming'">
                            <template x-if="upcomingDueExpenses().length === 0">
                                <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming expenses due in the
                                    next 30 days.</p>
                            </template>
                            <div x-show="upcomingDueExpenses().length > 0"
                                class="space-y-2 max-h-[60vh] overflow-y-auto pr-1 hide-scrollbar">
                                <template x-for="(expense, expenseIndex) in upcomingDueExpenses()"
                                    :key="`upcoming-modal-${expense.monthId}-${expense.expenseId ?? expenseIndex}`">
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-x-3 gap-y-1 items-start rounded-lg bg-white/40 dark:bg-slate-800/40 p-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100"
                                                x-text="expense.label || 'â€”'"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"
                                                x-text="`${expense.monthLabel} • ${expense.due_date ? formatPlannerDateOnly(expense.due_date) : 'No due date'}`">
                                            </p>
                                            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium"
                                                x-text="formatDaysUntil(expense.due_date)"></p>
                                        </div>
                                        <p class="shrink-0 text-sm font-semibold text-emerald-600 dark:text-emerald-300"
                                            x-text="formatCurrency(expense.amount)"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Overdue Expenses list -->
                        <div x-show="dueModalType === 'overdue'">
                            <template x-if="overdueExpenses().length === 0">
                                <p class="text-sm text-gray-500 dark:text-gray-400">No overdue expenses.</p>
                            </template>
                            <div x-show="overdueExpenses().length > 0"
                                class="space-y-2 max-h-[60vh] overflow-y-auto pr-1 hide-scrollbar">
                                <template x-for="(expense, expenseIndex) in overdueExpenses()"
                                    :key="`overdue-modal-${expense.monthId}-${expense.expenseId ?? expenseIndex}`">
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-x-3 gap-y-1 items-start rounded-lg bg-white/40 dark:bg-slate-800/40 p-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100"
                                                x-text="expense.label || 'â€”'"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"
                                                x-text="`${expense.monthLabel} • ${expense.due_date ? formatPlannerDateOnly(expense.due_date) : 'No due date'}`">
                                            </p>
                                            <p class="text-xs text-rose-600 dark:text-rose-400 font-medium"
                                                x-text="formatOverdueText(expense.due_date)"></p>
                                        </div>
                                        <p class="shrink-0 text-sm font-semibold text-rose-600 dark:text-rose-300"
                                            x-text="formatCurrency(expense.amount)"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" @click="showDueModal = false"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
