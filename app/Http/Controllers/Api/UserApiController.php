<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use User;

// app/Http/Controllers/Api/UserController.php
class UserApiController extends Controller
{
    public function index() {}           // GET /api/users
    public function store(Request $request) {}    // POST /api/users
    public function show(User $user) {}          // GET /api/users/{id}
    public function update(Request $request, User $user) {} // PUT/PATCH /api/users/{id}
    public function destroy(User $user) {}       // DELETE /api/users/{id}

}
