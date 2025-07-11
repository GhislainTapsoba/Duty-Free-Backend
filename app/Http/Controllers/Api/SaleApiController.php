<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/SaleController.php
class SaleApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(Sale $sale) {}
    public function update(Request $request, Sale $sale) {}
    public function destroy(Sale $sale) {}
    public function items(Sale $sale) {}
    public function complete(Sale $sale) {}
    public function cancel(Sale $sale) {}
    public function byRegister(CashRegister $register) {}
    public function byUser(User $user) {}
    public function search(Request $request) {}
    public function dailyReport() {}
    public function monthlyReport() {}
}