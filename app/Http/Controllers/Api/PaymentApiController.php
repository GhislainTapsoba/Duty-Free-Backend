<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function index()
    {
        return Payment::with('sale')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'exchange_rate' => 'nullable|numeric|min:0',
            'amount_in_base_currency' => 'nullable|numeric|min:0',
            'card_type' => 'nullable|string',
            'card_last_four' => 'nullable|string',
            'transaction_reference' => 'nullable|string',
            'authorization_code' => 'nullable|string',
            'mobile_money_provider' => 'nullable|string',
            'mobile_money_number' => 'nullable|string',
            'terminal_id' => 'nullable|string',
            'merchant_id' => 'nullable|string',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_date' => 'nullable|date',
        ]);

        $payment = Payment::create($data);
        return response()->json($payment->load('sale'), 201);
    }

    public function show($id)
    {
        return Payment::with('sale')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $data = $request->validate([
            'payment_method' => 'sometimes|string',
            'amount' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string',
            'exchange_rate' => 'nullable|numeric|min:0',
            'amount_in_base_currency' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_date' => 'nullable|date',
        ]);

        $payment->update($data);
        return response()->json($payment->load('sale'));
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return response()->json(['message' => 'Paiement supprimé avec succès.']);
    }
}
