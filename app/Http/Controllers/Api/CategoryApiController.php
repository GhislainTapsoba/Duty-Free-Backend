<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/CategoryController.php
class CategoryApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(Category $category) {}
    public function update(Request $request, Category $category) {}
    public function destroy(Category $category) {}
    public function publicIndex() {}
}
