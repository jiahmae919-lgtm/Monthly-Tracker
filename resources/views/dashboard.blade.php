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
                    + Open Planner Input
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
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Search</label>
                                <input
                                    type="text"
                                    x-model.trim="postedSearch"
                                    placeholder="Search month label (e.g. April, May)"
                                    class="h-10 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1.5">Remaining Filter</label>
                                <select
                                    x-model="postedFilter"
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
                            <template x-for="posted in filteredPostedMonths()" :key="posted.id">
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function monthlyPlannerApp() {
                return {
                    showPlannerModal: false,
                    showEditModal: false,
                    months: [],
                    postedMonths: @json($plannerEntries),
                    postedSearch: '',
                    postedFilter: 'all',
                    postedSort: 'latest',
                    subtotalRemaining: Number(@json($remainingSubtotal)) || 0,
                    editingEntry: null,
                    chart: null,
                    addMonth() {
                        this.months.push({
                            id: Date.now() + Math.random(),
                            label: '',
                            salary: 0,
                            cash: 0,
                            expenses: [{ id: Date.now() + Math.random(), label: '', amount: 0 }],
                        });
                    },
                    openPlannerModal() {
                        this.showPlannerModal = true;
                        if (this.months.length === 0) {
                            this.addMonth();
                        }
                    },
                    openEditModal(posted) {
                        this.editingEntry = {
                            id: posted.id,
                            label: posted.label || '',
                            salary: Number(posted.salary) || 0,
                            cash: Number(posted.cash) || 0,
                            expenses: (posted.expenses || []).map((expense) => ({
                                id: Date.now() + Math.random(),
                                label: expense.label || '',
                                amount: Number(expense.amount) || 0,
                            })),
                        };
                        if (this.editingEntry.expenses.length === 0) {
                            this.addEditExpense();
                        }
                        this.showEditModal = true;
                    },
                    closeEditModal() {
                        this.showEditModal = false;
                        this.editingEntry = null;
                    },
                    addEditExpense() {
                        if (!this.editingEntry) return;
                        this.editingEntry.expenses.push({
                            id: Date.now() + Math.random(),
                            label: '',
                            amount: 0,
                        });
                    },
                    removeEditExpense(expenseIndex) {
                        if (!this.editingEntry) return;
                        this.editingEntry.expenses.splice(expenseIndex, 1);
                    },
                    editTotalExpenses() {
                        if (!this.editingEntry) return 0;
                        return this.editingEntry.expenses.reduce((sum, expense) => sum + (Number(expense.amount) || 0), 0);
                    },
                    editTotalCash() {
                        if (!this.editingEntry) return 0;
                        return (Number(this.editingEntry.cash) || 0) + (Number(this.editingEntry.salary) || 0);
                    },
                    editRemaining() {
                        return this.editTotalCash() - this.editTotalExpenses();
                    },
                    removeMonth(monthIndex) {
                        this.months.splice(monthIndex, 1);
                    },
                    addExpense(monthIndex) {
                        this.months[monthIndex].expenses.push({
                            id: Date.now() + Math.random(),
                            label: '',
                            amount: 0,
                        });
                    },
                    removeExpense(monthIndex, expenseIndex) {
                        this.months[monthIndex].expenses.splice(expenseIndex, 1);
                    },
                    async postMonth(monthIndex) {
                        const month = this.months[monthIndex];
                        const cleanLabel = (month.label || '').trim();

                        if (!cleanLabel) {
                            alert('Please enter a month label.');
                            return;
                        }

                        const payload = {
                            label: cleanLabel,
                            salary: Number(month.salary) || 0,
                            cash: Number(month.cash) || 0,
                            expenses: month.expenses.map((expense) => ({
                                label: (expense.label || '').trim(),
                                amount: Number(expense.amount) || 0,
                            })),
                        };

                        const response = await fetch("{{ route('planner-entries.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload),
                        });

                        if (!response.ok) {
                            const errorData = await response.json().catch(() => ({}));
                            alert(errorData.message || 'Unable to save month note.');
                            return;
                        }

                        const data = await response.json();
                        this.postedMonths.unshift(data.entry);
                        this.months.splice(monthIndex, 1);
                        if (this.months.length === 0) {
                            this.showPlannerModal = false;
                        }
                        this.refreshSummary();
                    },
                    async deletePostedById(entryId) {
                        const item = this.postedMonths.find((entry) => entry.id === entryId);
                        if (!item) {
                            return;
                        }
                        const response = await fetch(`/planner-entries/${item.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            alert('Unable to delete posted note.');
                            return;
                        }

                        this.postedMonths = this.postedMonths.filter((entry) => entry.id !== entryId);
                        this.refreshSummary();
                    },
                    filteredPostedMonths() {
                        const search = this.postedSearch.toLowerCase();
                        let result = [...this.postedMonths];

                        if (search) {
                            result = result.filter((item) => (item.label || '').toLowerCase().includes(search));
                        }

                        if (this.postedFilter === 'positive') {
                            result = result.filter((item) => Number(item.remaining) > 0);
                        } else if (this.postedFilter === 'negative') {
                            result = result.filter((item) => Number(item.remaining) < 0);
                        } else if (this.postedFilter === 'zero') {
                            result = result.filter((item) => Number(item.remaining) === 0);
                        }

                        if (this.postedSort === 'oldest') {
                            result.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0));
                        } else if (this.postedSort === 'remaining_desc') {
                            result.sort((a, b) => Number(b.remaining) - Number(a.remaining));
                        } else if (this.postedSort === 'remaining_asc') {
                            result.sort((a, b) => Number(a.remaining) - Number(b.remaining));
                        } else if (this.postedSort === 'label_asc') {
                            result.sort((a, b) => (a.label || '').localeCompare(b.label || ''));
                        } else if (this.postedSort === 'label_desc') {
                            result.sort((a, b) => (b.label || '').localeCompare(a.label || ''));
                        } else {
                            result.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
                        }

                        return result;
                    },
                    async saveEdit() {
                        if (!this.editingEntry) return;
                        const cleanLabel = (this.editingEntry.label || '').trim();
                        if (!cleanLabel) {
                            alert('Please enter a month label.');
                            return;
                        }

                        const payload = {
                            label: cleanLabel,
                            salary: Number(this.editingEntry.salary) || 0,
                            cash: Number(this.editingEntry.cash) || 0,
                            expenses: this.editingEntry.expenses.map((expense) => ({
                                label: (expense.label || '').trim(),
                                amount: Number(expense.amount) || 0,
                            })),
                        };

                        const response = await fetch(`/planner-entries/${this.editingEntry.id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload),
                        });

                        if (!response.ok) {
                            const errorData = await response.json().catch(() => ({}));
                            alert(errorData.message || 'Unable to update posted note.');
                            return;
                        }

                        const data = await response.json();
                        const targetIndex = this.postedMonths.findIndex((item) => item.id === data.entry.id);
                        if (targetIndex !== -1) {
                            this.postedMonths[targetIndex] = data.entry;
                        }
                        this.closeEditModal();
                        this.refreshSummary();
                    },
                    totalExpenses(month) {
                        return month.expenses.reduce((sum, expense) => sum + (Number(expense.amount) || 0), 0);
                    },
                    totalCash(month) {
                        return (Number(month.cash) || 0) + (Number(month.salary) || 0);
                    },
                    remaining(month) {
                        return this.totalCash(month) - this.totalExpenses(month);
                    },
                    formatCurrency(amount) {
                        return new Intl.NumberFormat('en-PH', {
                            style: 'currency',
                            currency: 'PHP',
                        }).format(Number(amount) || 0);
                    },
                    refreshSummary() {
                        this.subtotalRemaining = this.postedMonths.reduce((sum, item) => sum + (Number(item.remaining) || 0), 0);
                        this.renderRemainingChart();
                    },
                    renderRemainingChart() {
                        const canvas = document.getElementById('monthlyRemainingChart');
                        if (!canvas) {
                            return;
                        }

                        const labels = this.postedMonths.map((item) => item.label);
                        const remainingData = this.postedMonths.map((item) => Number(item.remaining) || 0);
                        const colors = remainingData.map((value) => value >= 0 ? 'rgba(16, 185, 129, 0.7)' : 'rgba(244, 63, 94, 0.7)');

                        if (this.chart) {
                            this.chart.destroy();
                        }

                        this.chart = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Monthly Earning',
                                    data: remainingData,
                                    backgroundColor: colors,
                                    borderColor: colors.map((color) => color.replace('0.7', '1')),
                                    borderWidth: 1,
                                    borderRadius: 10,
                                    borderSkipped: false,
                                    maxBarThickness: 58,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: (context) => ` Earning: ${this.formatCurrency(context.raw)}`
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            color: '#94a3b8'
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: 'rgba(148, 163, 184, 0.15)'
                                        },
                                        ticks: {
                                            color: '#94a3b8',
                                            callback: (value) => this.formatCurrency(value)
                                        }
                                    }
                                }
                            }
                        });
                    },
                    init() {
                        this.refreshSummary();
                    }
                };
            }
        </script>
    @endpush
</x-app-layout>
