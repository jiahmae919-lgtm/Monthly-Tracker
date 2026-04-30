        <!-- View Posted Modal -->
        <div x-show="showViewModal" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm"
            @click="closeViewModal()"></div>
        <div x-show="showViewModal" x-transition class="fixed inset-0 z-50 overflow-y-auto p-4 md:p-8">
            <div class="min-h-full flex items-start justify-center">
                <div class="w-full max-w-4xl rounded-2xl border border-white/25 bg-white/20 dark:bg-slate-900/35 backdrop-blur-xl shadow-2xl"
                    @click.stop>
                    <template x-if="viewingEntry">
                        <div class="p-5 md:p-6 space-y-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Month Details
                                    </h3>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">Review the month details and
                                        expenses.</p>
                                </div>
                                <button type="button" @click="closeViewModal()"
                                    class="h-9 w-9 rounded-full bg-white/50 hover:bg-white/70 dark:bg-slate-700/70 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-100 text-lg leading-none">
                                    &times;
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Label</label>
                                        <div
                                            class="h-11 flex items-center px-3 rounded-lg bg-white/50 dark:bg-slate-900/50 text-gray-700 dark:text-gray-100 font-semibold">
                                            <span
                                                x-text="`${viewingEntry?.label || ''}${viewingEntry?.year ? ' ' + viewingEntry.year : ''}`.trim()"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Sahod</label>
                                        <div
                                            class="h-11 flex items-center px-3 rounded-lg bg-white/50 dark:bg-slate-900/50 text-gray-700 dark:text-gray-100 font-semibold">
                                            <span x-text="formatCurrency(viewingEntry?.salary)"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Cash
                                            on Hand</label>
                                        <div
                                            class="h-11 flex items-center px-3 rounded-lg bg-white/50 dark:bg-slate-900/50 text-gray-700 dark:text-gray-100 font-semibold">
                                            <span x-text="formatCurrency(viewingEntry?.cash)"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-white/30 dark:border-gray-700/50 my-4"></div>

                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Expenses
                                    </h4>
                                    <template x-if="!viewingEntry?.expenses || viewingEntry.expenses.length === 0">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No expenses</p>
                                    </template>
                                    <div x-show="viewingEntry?.expenses && viewingEntry.expenses.length > 0"
                                        class="space-y-3">
                                        <template x-for="(expense, expenseIndex) in viewingEntry.expenses"
                                            :key="(viewingEntry?.id ?? 'v') + '-' + expenseIndex">
                                            <div
                                                class="grid grid-cols-1 lg:grid-cols-12 gap-3 p-3 rounded-lg bg-white/40 dark:bg-slate-800/40 items-center">
                                                <div class="lg:col-span-4">
                                                    <p
                                                        class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400 mb-1">
                                                        Name</p>
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100"
                                                        x-text="expense.label || 'â€”'"></p>
                                                </div>
                                                <div class="lg:col-span-3">
                                                    <p
                                                        class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400 mb-1">
                                                        Due Date</p>
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100"
                                                        x-text="formatPlannerDateOnly(expense.due_date)"></p>
                                                </div>
                                                <div class="lg:col-span-2">
                                                    <p
                                                        class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400 mb-1">
                                                        Paid</p>
                                                    <div class="space-y-2">
                                                        <button type="button"
                                                            @click="toggleViewExpensePaid(expenseIndex)"
                                                            :disabled="viewingExpenseSavingIndex !== null"
                                                            :aria-pressed="expense.paid ? 'true' : 'false'"
                                                            :title="expense.paid ? 'Mark as unpaid' : 'Mark as paid'"
                                                            :aria-label="expense.paid ? 'Paid. Click to mark as unpaid' :
                                                                'Unpaid. Click to mark as paid'"
                                                            :class="expense.paid ?
                                                                'bg-emerald-500 hover:bg-emerald-600 text-white border-emerald-500/80 shadow-sm shadow-emerald-900/20' :
                                                                'bg-amber-500/15 hover:bg-amber-500/25 text-amber-700 border-amber-500/30 dark:text-amber-200 dark:border-amber-500/40'"
                                                            class="h-7 w-7 shrink-0 inline-flex items-center justify-center rounded-md border transition disabled:opacity-50 disabled:cursor-not-allowed">
                                                            <span x-show="viewingExpenseSavingIndex === expenseIndex"
                                                                class="inline-flex items-center justify-center"
                                                                aria-hidden="true">
                                                                <svg class="animate-spin h-3.5 w-3.5" fill="none"
                                                                    viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12"
                                                                        cy="12" r="10" stroke="currentColor"
                                                                        stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor"
                                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                            <span x-show="viewingExpenseSavingIndex !== expenseIndex"
                                                                class="inline-flex items-center justify-center"
                                                                aria-hidden="true">
                                                                <svg x-show="!expense.paid" class="h-3.5 w-3.5"
                                                                    fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor" stroke-width="2.25">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                                <svg x-show="expense.paid" class="h-3.5 w-3.5"
                                                                    fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor" stroke-width="2.5">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </span>
                                                        </button>
                                                        <span
                                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                                            :class="expenseStatusClasses(expense)"
                                                            x-text="expenseStatusLabel(expense)"></span>
                                                    </div>
                                                </div>
                                                <div class="lg:col-span-3 lg:text-right">
                                                    <p
                                                        class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400 mb-1">
                                                        Amount</p>
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100"
                                                        x-text="formatCurrency(expense.amount)"></p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="border-t border-white/30 dark:border-gray-700/50 my-4"></div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                    <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                        <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Total Expenses</p>
                                        <p class="mt-1 text-xl font-bold text-rose-600 dark:text-rose-400"
                                            x-text="formatCurrency(viewingEntry?.total_expenses)"></p>
                                    </div>
                                    <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                        <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Total Cash (saved)</p>
                                        <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100"
                                            x-text="formatCurrency(viewingEntry?.total_cash)"></p>
                                    </div>
                                    <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                        <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Current Balance</p>
                                        <p class="mt-1 text-xl font-bold"
                                            :class="(Number(viewingEntry?.total_cash) || 0) - Number(totalPaidExpenses(
                                                    viewingEntry) || 0) >= 0 ?
                                                'text-emerald-600 dark:text-emerald-400' :
                                                'text-rose-600 dark:text-rose-400'"
                                            x-text="formatCurrency((Number(viewingEntry?.total_cash) || 0) - Number(totalPaidExpenses(viewingEntry) || 0))">
                                        </p>
                                    </div>
                                    <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                        <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Overall Balance</p>
                                        <p class="mt-1 text-xl font-bold"
                                            :class="Number(viewingEntry?.remaining) >= 0 ?
                                                'text-emerald-600 dark:text-emerald-400' :
                                                'text-rose-600 dark:text-rose-400'"
                                            x-text="formatCurrency(viewingEntry?.remaining)"></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                        <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">1st
                                            Half Total (Up to 14th)</p>
                                        <p class="mt-1 text-xl font-bold text-emerald-600 dark:text-emerald-400"
                                            x-text="formatCurrency(totalExpensesFirstHalf(viewingEntry))"></p>
                                    </div>
                                    <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                        <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">2nd
                                            Half Total (Up to 30th)</p>
                                        <p class="mt-1 text-xl font-bold text-emerald-600 dark:text-emerald-400"
                                            x-text="formatCurrency(totalExpensesSecondHalf(viewingEntry))"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="button" @click="closeViewModal()"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">Close</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
