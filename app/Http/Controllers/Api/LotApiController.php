<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/LotController.php
class LotApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(Lot $lot) {}
    public function update(Request $request, Lot $lot) {}
    public function destroy(Lot $lot) {}
    public function expiring() {}
    public function expired() {}
}
