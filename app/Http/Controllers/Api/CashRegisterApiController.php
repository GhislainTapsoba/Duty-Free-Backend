<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/CashRegisterController.php
class CashRegisterApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(CashRegister $cashRegister) {}
    public function update(Request $request, CashRegister $cashRegister) {}
    public function destroy(CashRegister $cashRegister) {}
    public function open(CashRegister $register) {}
    public function close(CashRegister $register) {}
    public function status(CashRegister $register) {}
}
