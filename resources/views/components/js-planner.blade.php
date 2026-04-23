@push('scripts')
        <style>
            /* Keep toasts above modals, but don't block app clicks */
            .notyf {
                /* Put Notyf above modal layers (z-40 / z-50) */
                z-index: 99999 !important;
                pointer-events: none !important;
            }

            .notyf__toast,
            .notyf__dismiss {
                pointer-events: auto !important;
            }
        </style>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
        <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
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
                    notyf: null,
                    chart: null,
                    expandedEntries: new Set(),
                    setupNotyf() {
                        if (this.notyf) {
                            return;
                        }
                        if (typeof Notyf === 'undefined') {
                            return;
                        }
                        this.notyf = new Notyf({
                            duration: 2000,
                            position: {
                                x: 'right',
                                y: 'top',
                            },
                            dismissible: true,
                            ripple: true,
                            types: [{
                                type: 'warning',
                                background: '#f59e0b',
                                icon: {
                                    className: 'notyf__icon--warning',
                                    tagName: 'span',
                                    text: '!',
                                },
                            }],
                        });
                    },
                    notifySuccess(message) {
                        this.setupNotyf();
                        if (this.notyf) {
                            const n = this.notyf.open({
                                type: 'success',
                                message,
                                duration: 2000,
                            });
                            // Hard-enforce close even if hover pauses timer
                            setTimeout(() => {
                                try {
                                    // Some builds don't remove a single toast reliably, so dismiss all.
                                    this.notyf.dismissAll();
                                    // Last-resort: ensure DOM is cleared.
                                    document.querySelectorAll('.notyf__toast').forEach((el) => el.remove());
                                } catch (e) {}
                            }, 2200);
                            return;
                        }
                        console.log(message);
                    },
                    notifyError(message) {
                        this.setupNotyf();
                        if (this.notyf) {
                            const n = this.notyf.open({
                                type: 'error',
                                message,
                                duration: 2000,
                            });
                            setTimeout(() => {
                                try {
                                    this.notyf.dismissAll();
                                    document.querySelectorAll('.notyf__toast').forEach((el) => el.remove());
                                } catch (e) {}
                            }, 2200);
                            return;
                        }
                        console.error(message);
                    },
                    notifyWarning(message) {
                        this.setupNotyf();
                        if (this.notyf) {
                            const n = this.notyf.open({
                                type: 'warning',
                                message,
                                duration: 2000,
                            });
                            setTimeout(() => {
                                try {
                                    this.notyf.dismissAll();
                                    document.querySelectorAll('.notyf__toast').forEach((el) => el.remove());
                                } catch (e) {}
                            }, 2200);
                            return;
                        }
                        console.warn(message);
                    },
                    extractBackendErrorMessage(errorData, fallbackMessage) {
                        if (!errorData) {
                            return fallbackMessage;
                        }
                        if (typeof errorData === 'string') {
                            return errorData;
                        }
                        if (errorData.message && typeof errorData.message === 'string' && errorData.message.trim()) {
                            return errorData.message;
                        }
                        // Laravel validation errors usually look like { message: "...", errors: { field: ["msg"] } }
                        if (errorData.errors && typeof errorData.errors === 'object') {
                            const firstKey = Object.keys(errorData.errors)[0];
                            const firstVal = firstKey ? errorData.errors[firstKey] : null;
                            const firstMsg = Array.isArray(firstVal) ? firstVal[0] : firstVal;
                            if (firstMsg) {
                                return String(firstMsg);
                            }
                        }
                        return fallbackMessage;
                    },
                    addMonth() {
                        const currentYear = new Date().getFullYear();
                        this.months.push({
                            id: Date.now() + Math.random(),
                            label: '',
                            year: currentYear,
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
                            year: Number(posted.year) || new Date().getFullYear(),
                            salary: Number(posted.salary) || 0,
                            cash: Number(posted.cash) || 0,
                            expenses: (posted.expenses || []).map((expense) => {
                                let due = expense.due_date;
                                if (due) {
                                    const s = String(due);
                                    due = s.length >= 10 ? s.slice(0, 10) : s;
                                } else {
                                    due = '';
                                }
                                return {
                                    id: Date.now() + Math.random(),
                                    label: expense.label || '',
                                    amount: Number(expense.amount) || 0,
                                    due_date: due,
                                    paid: expense.paid || false,
                                };
                            }),
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
                        const expenseName = (target.label || '').trim() || 'Expense';
                        const payload = {
                            label: (entry.label || '').trim(),
                            year: Number(entry.year) || new Date().getFullYear(),
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
                                this.notifyError(this.extractBackendErrorMessage(errorData, 'Unable to update paid status.'));
                                return;
                            }
                            const data = await response.json();
                            const listIndex = this.postedMonths.findIndex((item) => item.id === data.entry.id);
                            if (listIndex !== -1) {
                                this.postedMonths[listIndex] = data.entry;
                            }
                            this.viewingEntry = data.entry;
                            this.refreshSummary();
                            this.notifySuccess(newPaid ? `${expenseName} is paid.` : `${expenseName} is unpaid.`);
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
                        const year = Number(month.year) || new Date().getFullYear();

                        if (!cleanLabel) {
                            this.notifyWarning('Please enter a month label.');
                            return;
                        }

                        const payload = {
                            label: cleanLabel,
                            year,
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
                            this.notifyError(this.extractBackendErrorMessage(errorData, 'Unable to save month note.'));
                            return;
                        }

                        const data = await response.json();
                        this.postedMonths.unshift(data.entry);
                        this.months.splice(monthIndex, 1);
                        if (this.months.length === 0) {
                            this.showPlannerModal = false;
                        }
                        this.refreshSummary();
                        this.notifySuccess(`${cleanLabel} ${year} added.`);
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
                            const errorData = await response.json().catch(() => ({}));
                            this.notifyError(this.extractBackendErrorMessage(errorData, 'Unable to delete posted note.'));
                            return;
                        }

                        const data = await response.json().catch(() => ({}));
                        this.postedMonths = this.postedMonths.filter((entry) => entry.id !== entryId);
                        this.currentPage = Math.max(1, Math.min(this.currentPage, this.getTotalPages()));
                        this.refreshSummary();
                        const label = (item.label || '').trim() || 'Month note';
                        this.notifySuccess(`${label} deleted.`);
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
                                if (day >= 15 && day <= 30) {
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
                        const year = Number(this.editingEntry.year) || new Date().getFullYear();
                        if (!cleanLabel) {
                            this.notifyWarning('Please enter a month label.');
                            return;
                        }

                        const payload = {
                            label: cleanLabel,
                            year,
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
                            this.notifyError(this.extractBackendErrorMessage(errorData, 'Unable to update posted note.'));
                            return;
                        }

                        const data = await response.json();
                        const targetIndex = this.postedMonths.findIndex((item) => item.id === data.entry.id);
                        if (targetIndex !== -1) {
                            this.postedMonths[targetIndex] = data.entry;
                        }
                        this.closeEditModal();
                        this.refreshSummary();
                        this.notifySuccess(`${cleanLabel} ${year} updated.`);
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

                        const ctx = canvas.getContext('2d');
                        const labels = this.postedMonths.map((item) => `${item.label} ${item.year || ''}`.trim());
                        const remainingData = this.postedMonths.map((item) => Number(item.remaining) || 0);
                        const trendData = remainingData.map((value, index, arr) => {
                            const prev = index > 0 ? arr[index - 1] : value;
                            const next = index < arr.length - 1 ? arr[index + 1] : value;
                            return (prev + value + next) / 3;
                        });

                        const barGradient = ctx.createLinearGradient(0, 0, 0, canvas.height || 300);
                        barGradient.addColorStop(0, 'rgba(70, 245, 255, 0.95)');
                        barGradient.addColorStop(0.5, 'rgba(115, 221, 255, 0.85)');
                        barGradient.addColorStop(1, 'rgba(58, 131, 255, 0.55)');

                        const lineGradient = ctx.createLinearGradient(0, 0, 0, canvas.height || 300);
                        lineGradient.addColorStop(0, 'rgba(134, 196, 255, 0.45)');
                        lineGradient.addColorStop(1, 'rgba(134, 196, 255, 0.03)');

                        if (this.chart) {
                            this.chart.destroy();
                        }

                        this.chart = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        type: 'bar',
                                        label: 'Monthly Remaining',
                                        data: remainingData,
                                        backgroundColor: barGradient,
                                        borderColor: 'rgba(120, 229, 255, 0.95)',
                                        borderWidth: 1.1,
                                        borderRadius: 8,
                                        borderSkipped: false,
                                        maxBarThickness: 26,
                                        barPercentage: 0.58,
                                        categoryPercentage: 0.72,
                                    },
                                    {
                                        type: 'line',
                                        label: 'Trend',
                                        data: trendData,
                                        borderColor: 'rgba(177, 225, 255, 0.95)',
                                        backgroundColor: lineGradient,
                                        fill: true,
                                        tension: 0.42,
                                        pointRadius: 2.5,
                                        pointHoverRadius: 4,
                                        pointBackgroundColor: 'rgba(214, 239, 255, 1)',
                                        pointBorderColor: 'rgba(214, 239, 255, 0.65)',
                                        pointBorderWidth: 2,
                                        borderWidth: 2,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        intersect: false,
                                        mode: 'index',
                                        callbacks: {
                                            label: (context) => ` ${context.dataset.label}: ${this.formatCurrency(context.raw)}`
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            color: '#9fb1d1',
                                            font: {
                                                size: 11,
                                                weight: '600',
                                            },
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: 'rgba(103, 86, 173, 0.28)',
                                            drawBorder: false,
                                        },
                                        ticks: {
                                            color: '#8ea3c3',
                                            font: {
                                                size: 11,
                                            },
                                            callback: (value) => new Intl.NumberFormat('en-US', {
                                                maximumFractionDigits: 0,
                                            }).format(Number(value) || 0),
                                        }
                                    }
                                }
                            }
                        });
                    },
                    init() {
                        this.setupNotyf();
                        this.refreshSummary();
                    }
                };
            }
        </script>
    @endpush
