<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use Illuminate\Http\Request;

class ProductReturnApiController extends Controller
{
    public function show($id)
    {
        $productReturn = ProductReturn::with('items')->find($id);

        if (!$productReturn) {
            return response()->json(['message' => 'Retour non trouvé'], 404);
        }

        return response()->json(['data' => $productReturn]);
    }

    // Ajoute d’autres méthodes (index, store, update, delete) selon besoins
}
