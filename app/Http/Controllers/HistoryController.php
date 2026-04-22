<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get all transactions (incomes, expenses, payments) for the user
        $incomes = Income::where('user_id', $user->id)
            ->selectRaw("'Income' as type, source as description, amount, date, 'green' as color")
            ->get();

        $expenses = Expense::where('user_id', $user->id)
            ->selectRaw("'Expense' as type, category as description, amount, date, 'red' as color")
            ->get();

        $payments = Payment::where('user_id', $user->id)
            ->join('debts', 'payments.debt_id', '=', 'debts.id')
            ->selectRaw("'Payment' as type, CONCAT('Payment for ', debts.name) as description, payments.amount, payments.date, 'blue' as color")
            ->get();

        // Combine all transactions and sort by date (most recent first)
        $allTransactions = $incomes->concat($expenses)->concat($payments)
            ->sortByDesc('date')
            ->values();

        // Apply filters if provided
        $type = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($type) {
            $allTransactions = $allTransactions->where('type', $type);
        }

        if ($startDate) {
            $allTransactions = $allTransactions->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $allTransactions = $allTransactions->where('date', '<=', $endDate);
        }

        // Paginate results
        $perPage = 20;
        $page = $request->get('page', 1);
        $paginatedTransactions = $allTransactions->forPage($page, $perPage);

        return view('history.index', compact('paginatedTransactions', 'allTransactions', 'type', 'startDate', 'endDate'));
    }
}
