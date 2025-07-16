<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class CashRegisterApiController extends Controller
{
    public function index(): JsonResponse
    {
        $cashRegisters = CashRegister::with(['openedBy', 'closedBy'])->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $cashRegisters
        ]);
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:cash_registers,code',
            'location' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
            'opening_balance' => 'required|numeric|min:0',
        ]);
        
        $cashRegister = CashRegister::create([
            'name' => $request->name,
            'code' => $request->code,
            'location' => $request->location,
            'ip_address' => $request->ip_address,
            'opening_balance' => $request->opening_balance,
            'current_balance' => $request->opening_balance,
            'is_active' => true,
            'is_open' => false,
            'printer_config' => $request->printer_config ?? [],
            'scanner_config' => $request->scanner_config ?? [],
            'tpe_config' => $request->tpe_config ?? [],
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Caisse créée avec succès',
            'data' => $cashRegister
        ], 201);
    }
    
    public function show(CashRegister $cashRegister): JsonResponse
    {
        $cashRegister->load(['sales', 'openedBy', 'closedBy']);
        
        return response()->json([
            'status' => 'success',
            'data' => $cashRegister
        ]);
    }
    
    public function update(Request $request, CashRegister $cashRegister): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:cash_registers,code,' . $cashRegister->id,
            'location' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
        ]);
        
        $cashRegister->update([
            'name' => $request->name,
            'code' => $request->code,
            'location' => $request->location,
            'ip_address' => $request->ip_address,
            'printer_config' => $request->printer_config ?? $cashRegister->printer_config,
            'scanner_config' => $request->scanner_config ?? $cashRegister->scanner_config,
            'tpe_config' => $request->tpe_config ?? $cashRegister->tpe_config,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Caisse mise à jour avec succès',
            'data' => $cashRegister
        ]);
    }
    
    public function open(Request $request, CashRegister $cashRegister): JsonResponse
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
        ]);
        
        if ($cashRegister->is_open) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cette caisse est déjà ouverte'
            ], 400);
        }
        
        $cashRegister->update([
            'is_open' => true,
            'opened_by' => Auth::id(),
            'opened_at' => now(),
            'opening_balance' => $request->opening_balance,
            'current_balance' => $request->opening_balance,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Caisse ouverte avec succès',
            'data' => $cashRegister
        ]);
    }
    
    public function close(CashRegister $cashRegister): JsonResponse
    {
        if (!$cashRegister->is_open) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cette caisse n\'est pas ouverte'
            ], 400);
        }
        
        $cashRegister->update([
            'is_open' => false,
            'closed_by' => Auth::id(),
            'closed_at' => now(),
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Caisse fermée avec succès',
            'data' => $cashRegister
        ]);
    }
    
    public function destroy(CashRegister $cashRegister): JsonResponse
    {
        if ($cashRegister->is_open) {
            return response()->json([
                'status' => 'error',
                'message' => 'Impossible de supprimer une caisse ouverte'
            ], 400);
        }
        
        $cashRegister->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Caisse supprimée avec succès'
        ]);
    }
}
