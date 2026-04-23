@push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function monthlyPlannerApp() {
                return {
                    showPlannerModal: false,
                    showEditModal: false,
                    showViewModal: false,
                    months: [],
                    postedMonths: @json($plannerEntries),
                    postedSearch: '',
                    postedFilter: 'all',
                    postedSort: 'latest',
                    currentPage: 1,
                    itemsPerPage: 5,
                    subtotalRemaining: Number(@json($remainingSubtotal)) || 0,
                    editingEntry: null,
                    viewingEntry: null,
                    viewingExpenseSavingIndex: null,
                    chart: null,
                    expandedEntries: new Set(),
                    addMonth() {
                        this.months.push({
                            id: Date.now() + Math.random(),
                            label: '',
                            salary: 0,
                            cash: 0,
                            expenses: [{
                                id: Date.now() + Math.random(),
                                label: '',
                                amount: 0,
                                due_date: '',
                                paid: false
                            }],
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
                                due_date: expense.due_date || '',
                                paid: expense.paid || false,
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
                    openViewModal(posted) {
                        this.viewingEntry = posted;
                        this.showViewModal = true;
                    },
                    closeViewModal() {
                        this.showViewModal = false;
                        this.viewingEntry = null;
                        this.viewingExpenseSavingIndex = null;
                    },
                    async toggleViewExpensePaid(expenseIndex) {
                        if (!this.viewingEntry || this.viewingExpenseSavingIndex !== null) {
                            return;
                        }
                        const entry = this.viewingEntry;
                        const target = entry.expenses[expenseIndex];
                        if (!target) {
                            return;
                        }
                        this.viewingExpenseSavingIndex = expenseIndex;
                        const newPaid = !target.paid;
                        const payload = {
                            label: (entry.label || '').trim(),
                            salary: Number(entry.salary) || 0,
                            cash: Number(entry.cash) || 0,
                            expenses: entry.expenses.map((e, i) => ({
                                label: (e.label || '').trim(),
                                amount: Number(e.amount) || 0,
                                due_date: e.due_date || null,
                                paid: i === expenseIndex ? newPaid : !!e.paid,
                            })),
                        };
                        try {
                            const response = await fetch(`/planner-entries/${entry.id}`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify(payload),
                            });
                            if (!response.ok) {
                                const errorData = await response.json().catch(() => ({}));
                                alert(errorData.message || 'Unable to update paid status.');
                                return;
                            }
                            const data = await response.json();
                            const listIndex = this.postedMonths.findIndex((item) => item.id === data.entry.id);
                            if (listIndex !== -1) {
                                this.postedMonths[listIndex] = data.entry;
                            }
                            this.viewingEntry = data.entry;
                            this.refreshSummary();
                        } finally {
                            this.viewingExpenseSavingIndex = null;
                        }
                    },
                    addEditExpense() {
                        if (!this.editingEntry) return;
                        this.editingEntry.expenses.push({
                            id: Date.now() + Math.random(),
                            label: '',
                            amount: 0,
                            due_date: '',
                            paid: false,
                        });
                    },
                    removeEditExpense(expenseIndex) {
                        if (!this.editingEntry) return;
                        this.editingEntry.expenses.splice(expenseIndex, 1);
                    },
                    toggleEditExpensePaid(expenseIndex) {
                        if (!this.editingEntry || !this.editingEntry.expenses[expenseIndex]) return;
                        this.editingEntry.expenses[expenseIndex].paid = !this.editingEntry.expenses[expenseIndex].paid;
                    },
                    togglePostedExpensePaid(postedIndex, expenseIndex) {
                        if (!this.postedMonths[postedIndex] || !this.postedMonths[postedIndex].expenses[expenseIndex]) return;
                        this.postedMonths[postedIndex].expenses[expenseIndex].paid = !this.postedMonths[postedIndex].expenses[expenseIndex].paid;
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
                            due_date: '',
                            paid: false,
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
                                due_date: expense.due_date || null,
                                paid: expense.paid || false,
                            })),
                        };

                        const response = await fetch("{{ route('planner-entries.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            alert('Unable to delete posted note.');
                            return;
                        }

                        this.postedMonths = this.postedMonths.filter((entry) => entry.id !== entryId);
                        this.currentPage = Math.max(1, Math.min(this.currentPage, this.getTotalPages()));
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
                    getPaginatedResults() {
                        const filtered = this.filteredPostedMonths();
                        const start = (this.currentPage - 1) * this.itemsPerPage;
                        const end = start + this.itemsPerPage;
                        return filtered.slice(start, end);
                    },
                    getTotalPages() {
                        const filtered = this.filteredPostedMonths();
                        return Math.ceil(filtered.length / this.itemsPerPage);
                    },
                    getPageNumbers() {
                        const totalPages = this.getTotalPages();
                        const pages = [];
                        const maxVisible = 5;

                        if (totalPages <= maxVisible) {
                            for (let i = 1; i <= totalPages; i++) {
                                pages.push(i);
                            }
                        } else {
                            pages.push(1);
                            if (this.currentPage > 3) {
                                pages.push('...');
                            }
                            const start = Math.max(2, this.currentPage - 1);
                            const end = Math.min(totalPages - 1, this.currentPage + 1);
                            for (let i = start; i <= end; i++) {
                                pages.push(i);
                            }
                            if (this.currentPage < totalPages - 2) {
                                pages.push('...');
                            }
                            pages.push(totalPages);
                        }

                        return pages;
                    },
                    totalExpensesFirstHalf(month = null) {
                        let total = 0;
                        const expenseList = month ? month.expenses : [];
                        expenseList.forEach(expense => {
                            if (expense.due_date) {
                                const day = new Date(expense.due_date).getDate();
                                if (day >= 1 && day <= 14) {
                                    total += Number(expense.amount) || 0;
                                }
                            }
                        });
                        return total;
                    },
                    totalExpensesSecondHalf(month = null) {
                        let total = 0;
                        const expenseList = month ? month.expenses : [];
                        expenseList.forEach(expense => {
                            if (expense.due_date) {
                                const day = new Date(expense.due_date).getDate();
                                if (day >= 15 && day <= 31) {
                                    total += Number(expense.amount) || 0;
                                }
                            }
                        });
                        return total;
                    },
                    toggleExpanded(entryId) {
                        if (this.expandedEntries.has(entryId)) {
                            this.expandedEntries.delete(entryId);
                        } else {
                            this.expandedEntries.add(entryId);
                        }
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
                                due_date: expense.due_date || null,
                                paid: expense.paid || false,
                            })),
                        };

                        const response = await fetch(`/planner-entries/${this.editingEntry.id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
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
                    formatPlannerDateOnly(value) {
                        if (!value) {
                            return '—';
                        }
                        const d = new Date(value + (value.length === 10 ? 'T00:00:00' : ''));
                        if (Number.isNaN(d.getTime())) {
                            return String(value);
                        }
                        return d.toLocaleDateString('en-US', {
                            timeZone: 'Asia/Manila',
                            dateStyle: 'medium',
                        });
                    },
                    refreshSummary() {
                        this.subtotalRemaining = this.postedMonths.reduce((sum, item) => sum + (Number(item.remaining) || 0),
                        0);
                        this.renderRemainingChart();
                    },
                    renderRemainingChart() {
                        const canvas = document.getElementById('monthlyRemainingChart');
                        if (!canvas) {
                            return;
                        }

                        const labels = this.postedMonths.map((item) => item.label);
                        const remainingData = this.postedMonths.map((item) => Number(item.remaining) || 0);
                        const colors = remainingData.map((value) => value >= 0 ? 'rgba(16, 185, 129, 0.7)' :
                            'rgba(244, 63, 94, 0.7)');

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
                                    legend: {
                                        display: false
                                    },
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
