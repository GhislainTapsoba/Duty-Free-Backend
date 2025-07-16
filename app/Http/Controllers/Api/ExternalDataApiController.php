<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExternalData;
use Illuminate\Http\Request;

class ExternalDataApiController extends Controller
{
    public function index()
    {
        return ExternalData::with('user')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'data_type' => 'required|string',
            'period_date' => 'required|date',
            'data' => 'required|array',
            'source' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $externalData = ExternalData::create($data);
        return response()->json($externalData->load('user'), 201);
    }

    public function show($id)
    {
        return ExternalData::with('user')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $externalData = ExternalData::findOrFail($id);

        $data = $request->validate([
            'data_type' => 'sometimes|string',
            'period_date' => 'sometimes|date',
            'data' => 'sometimes|array',
            'source' => 'nullable|string',
        ]);

        $externalData->update($data);
        return response()->json($externalData->load('user'));
    }

    public function destroy($id)
    {
        $externalData = ExternalData::findOrFail($id);
        $externalData->delete();

        return response()->json(['message' => 'Donnée supprimée avec succès.']);
    }
}
