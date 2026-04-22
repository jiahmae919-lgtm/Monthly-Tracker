<?php

namespace App\Http\Controllers;

use App\Models\MonthlyPlannerEntry;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function formatPlannerEntry(MonthlyPlannerEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'label' => $entry->month_label,
            'salary' => (float) $entry->salary,
            'cash' => (float) $entry->cash_on_hand,
            'total_expenses' => (float) $entry->total_expenses,
            'total_cash' => (float) $entry->total_cash,
            'remaining' => (float) $entry->remaining,
            'expenses' => $entry->expenses ?? [],
            'created_at' => $entry->created_at?->toDateTimeString(),
        ];
    }

    private function validatePlannerPayload(Request $request): array
    {
        return $request->validate([
            'label' => 'required|string|max:255',
            'salary' => 'required|numeric',
            'cash' => 'required|numeric',
            'expenses' => 'required|array|min:1',
            'expenses.*.label' => 'nullable|string|max:255',
            'expenses.*.amount' => 'required|numeric',
            'expenses.*.paid' => 'boolean',
        ]);
    }

    public function index()
    {
        $user = auth()->user();
        $plannerEntries = $user->monthlyPlannerEntries()
            ->latest()
            ->get()
            ->map(fn (MonthlyPlannerEntry $entry) => $this->formatPlannerEntry($entry))
            ->values();

        $remainingSubtotal = $plannerEntries->sum('remaining');

        return view('dashboard', [
            'plannerEntries' => $plannerEntries,
            'remainingSubtotal' => $remainingSubtotal,
        ]);
    }

    public function storePlannerEntry(Request $request)
    {
        $validated = $this->validatePlannerPayload($request);

        $normalizedExpenses = collect($validated['expenses'])->map(function ($expense) {
            return [
                'label' => trim((string) ($expense['label'] ?? '')),
                'amount' => (float) ($expense['amount'] ?? 0),
            ];
        })->values();

        $totalExpenses = $normalizedExpenses->sum('amount');
        $salary = (float) $validated['salary'];
        $cash = (float) $validated['cash'];
        $totalCash = $salary + $cash;
        $remaining = $totalCash - $totalExpenses;

        $entry = $request->user()->monthlyPlannerEntries()->create([
            'month_label' => $validated['label'],
            'salary' => $salary,
            'cash_on_hand' => $cash,
            'total_expenses' => $totalExpenses,
            'total_cash' => $totalCash,
            'remaining' => $remaining,
            'expenses' => $normalizedExpenses->all(),
        ]);

        return response()->json([
            'message' => 'Monthly planner note saved.',
            'entry' => $this->formatPlannerEntry($entry),
        ]);
    }

    public function updatePlannerEntry(Request $request, MonthlyPlannerEntry $entry)
    {
        $ownedEntry = $request->user()->monthlyPlannerEntries()->findOrFail($entry->id);
        $validated = $this->validatePlannerPayload($request);

        $normalizedExpenses = collect($validated['expenses'])->map(function ($expense) {
            return [
                'label' => trim((string) ($expense['label'] ?? '')),
                'amount' => (float) ($expense['amount'] ?? 0),
            ];
        })->values();

        $totalExpenses = $normalizedExpenses->sum('amount');
        $salary = (float) $validated['salary'];
        $cash = (float) $validated['cash'];
        $totalCash = $salary + $cash;
        $remaining = $totalCash - $totalExpenses;

        $ownedEntry->update([
            'month_label' => $validated['label'],
            'salary' => $salary,
            'cash_on_hand' => $cash,
            'total_expenses' => $totalExpenses,
            'total_cash' => $totalCash,
            'remaining' => $remaining,
            'expenses' => $normalizedExpenses->all(),
        ]);

        return response()->json([
            'message' => 'Monthly planner note updated.',
            'entry' => $this->formatPlannerEntry($ownedEntry->fresh()),
        ]);
    }

    public function destroyPlannerEntry(Request $request, MonthlyPlannerEntry $entry)
    {
        $ownedEntry = $request->user()->monthlyPlannerEntries()->findOrFail($entry->id);
        $ownedEntry->delete();

        return response()->json([
            'message' => 'Monthly planner note deleted.',
        ]);
    }
}
