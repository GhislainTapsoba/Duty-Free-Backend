<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PosApiController extends Controller
{
    public function index()
    {
        // Exemple : liste des caisses / point de vente
        return response()->json([
            'message' => 'Liste des points de vente - à implémenter'
        ]);
    }
}
