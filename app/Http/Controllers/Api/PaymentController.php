<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->payments()->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'receipt' => 'nullable|file|image|max:10240', // 10MB max
            'date_paid' => 'required|date',
        ]);

        $path = null;
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
        }

        $payment = Payment::create([
            'resident_id' => $request->user()->id,
            'amount' => $request->amount,
            'status' => PaymentStatus::Pending,
            'date_paid' => $request->date_paid,
            'receipt_url' => $path ? asset('storage/' . $path) : null,
        ]);

        return response()->json($payment, 201);
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        return $payment;
    }
}
