        <!-- Edit Posted Modal -->
        <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm"
            @click="closeEditModal()"></div>
        <div x-show="showEditModal" x-transition class="fixed inset-0 z-50 overflow-y-auto p-4 md:p-8">
            <div class="min-h-full flex items-start justify-center">
                <div class="w-full max-w-4xl rounded-2xl border border-white/25 bg-white/20 dark:bg-slate-900/35 backdrop-blur-xl shadow-2xl"
                    @click.stop>
                    <template x-if="editingEntry">
                        <div class="p-5 md:p-6 space-y-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Saved
                                        Record
                                    </h3>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">Update the details and save
                                        your changes.</p>
                                </div>
                                <button type="button" @click="closeEditModal()"
                                    class="h-9 w-9 rounded-full bg-white/50 hover:bg-white/70 dark:bg-slate-700/70 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-100 text-lg leading-none">&times;</button>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                                <div class="lg:col-span-5">
                                    <label
                                        class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Month
                                        Label</label>
                                    <input type="text" x-model="editingEntry.label"
                                        class="h-11 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="lg:col-span-2">
                                    <label
                                        class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Year</label>
                                    <input type="number" min="2000" max="2100" step="1"
                                        x-model.number="editingEntry.year"
                                        class="h-11 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="lg:col-span-2">
                                    <label
                                        class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Sahod</label>
                                    <input type="number" step="0.01" x-model.number="editingEntry.salary"
                                        class="h-11 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="lg:col-span-3">
                                    <label
                                        class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Cash
                                        on Hand</label>
                                    <input type="number" step="0.01" x-model.number="editingEntry.cash"
                                        class="h-11 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Expense List
                                    </h4>
                                    <button type="button" @click="addEditExpense()"
                                        class="bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2 px-3 rounded-lg text-xs">+
                                        Add Expense</button>
                                </div>
                                <template x-for="(expense, expenseIndex) in editingEntry.expenses"
                                    :key="expense.id">
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
                                        <div class="lg:col-span-4">
                                            <label
                                                class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Name</label>
                                            <input type="text" x-model="expense.label"
                                                class="h-10 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div class="lg:col-span-3">
                                            <label
                                                class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Due
                                                Date</label>
                                            <input type="date" x-model="expense.due_date"
                                                class="h-10 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div class="lg:col-span-3">
                                            <label
                                                class="block text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300 mb-1.5">Amount</label>
                                            <input type="number" step="0.01" x-model.number="expense.amount"
                                                class="h-10 block w-full border-white/40 bg-white/70 dark:bg-slate-900/70 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div class="lg:col-span-2">
                                            <button type="button" @click="removeEditExpense(expenseIndex)"
                                                class="w-full h-10 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-semibold rounded-lg text-sm">X</button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                    <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">Total
                                        Expenses</p>
                                    <p class="mt-1 text-xl font-bold text-rose-600 dark:text-rose-400"
                                        x-text="formatCurrency(editTotalExpenses())"></p>
                                </div>
                                <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                    <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">Total
                                        Cash
                                    </p>
                                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100"
                                        x-text="formatCurrency(editTotalCash())"></p>
                                </div>
                                <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                    <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">Balance
                                    </p>
                                    <p class="mt-1 text-xl font-bold"
                                        :class="editRemaining() >= 0 ? 'text-emerald-600 dark:text-emerald-400' :
                                            'text-rose-600 dark:text-rose-400'"
                                        x-text="formatCurrency(editRemaining())"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                    <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">1st
                                        Half Total (Up to 14th)</p>
                                    <p class="mt-1 text-xl font-bold text-emerald-600 dark:text-emerald-400"
                                        x-text="formatCurrency(totalExpensesFirstHalf(editingEntry))"></p>
                                </div>
                                <div class="p-4 rounded-lg bg-white/60 dark:bg-slate-900/55">
                                    <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300">2nd
                                        Half Total (Up to 30th)</p>
                                    <p class="mt-1 text-xl font-bold text-emerald-600 dark:text-emerald-400"
                                        x-text="formatCurrency(totalExpensesSecondHalf(editingEntry))"></p>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="closeEditModal()"
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">Cancel</button>
                                <button type="button" @click="saveEdit()"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">Save
                                    Changes</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
