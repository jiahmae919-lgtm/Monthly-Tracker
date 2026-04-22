<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $debts = auth()->user()->debts()->with('payments')->latest()->paginate(10);
        return view('debts.index', compact('debts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('debts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'monthly_payment' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['remaining_balance'] = $request->total_amount;

        auth()->user()->debts()->create($data);

        return redirect()->route('debts.index')->with('success', 'Debt added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt)
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }
        $payments = $debt->payments()->latest()->paginate(10);
        return view('debts.show', compact('debt', 'payments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debt $debt)
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }
        return view('debts.edit', compact('debt'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Debt $debt)
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'monthly_payment' => 'required|numeric|min:0',
            'remaining_balance' => 'required|numeric|min:0',
        ]);

        $debt->update($request->all());

        return redirect()->route('debts.index')->with('success', 'Debt updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt)
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }
        $debt->delete();
        return redirect()->route('debts.index')->with('success', 'Debt deleted successfully.');
    }
}
