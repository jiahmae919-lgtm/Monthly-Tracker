<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Not used, payments shown in debt show
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $debt = Debt::findOrFail($request->debt_id);
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }
        return view('payments.create', compact('debt'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $debt = Debt::findOrFail($request->debt_id);
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $debt->remaining_balance,
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment = $debt->payments()->create($request->only(['amount', 'date', 'notes']));

        $debt->decrement('remaining_balance', $request->amount);

        return redirect()->route('debts.show', $debt)->with('success', 'Payment added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        if ($payment->debt->user_id !== auth()->id()) {
            abort(403);
        }
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        if ($payment->debt->user_id !== auth()->id()) {
            abort(403);
        }
        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        if ($payment->debt->user_id !== auth()->id()) {
            abort(403);
        }

        $oldAmount = $payment->amount;

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment->update($request->only(['amount', 'date', 'notes']));

        $debt = $payment->debt;
        $debt->remaining_balance += $oldAmount - $request->amount;
        $debt->save();

        return redirect()->route('debts.show', $debt)->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        if ($payment->debt->user_id !== auth()->id()) {
            abort(403);
        }

        $debt = $payment->debt;
        $debt->increment('remaining_balance', $payment->amount);
        $payment->delete();

        return redirect()->route('debts.show', $debt)->with('success', 'Payment deleted successfully.');
    }
}
