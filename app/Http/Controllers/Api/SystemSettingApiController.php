<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingApiController extends Controller
{
    public function index()
    {
        return SystemSetting::all();
    }

    public function show($key)
    {
        $setting = SystemSetting::where('key', $key)->first();

        if (!$setting) {
            return response()->json(['message' => 'Paramètre introuvable'], 404);
        }

        return response()->json([
            'key' => $setting->key,
            'value' => SystemSetting::getValue($key)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|unique:system_settings,key',
            'value' => 'required',
            'type' => 'required|string|in:string,integer,boolean,json',
            'group' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $setting = SystemSetting::setValue($data['key'], $data['value'], $data['type'], $data['group'] ?? 'general');
        return response()->json($setting, 201);
    }

    public function update(Request $request, $key)
    {
        $setting = SystemSetting::where('key', $key)->firstOrFail();

        $data = $request->validate([
            'value' => 'required',
            'type' => 'required|string|in:string,integer,boolean,json',
            'group' => 'nullable|string',
        ]);

        $updated = SystemSetting::setValue($key, $data['value'], $data['type'], $data['group'] ?? $setting->group);
        return response()->json($updated);
    }

    public function destroy($key)
    {
        $setting = SystemSetting::where('key', $key)->firstOrFail();
        $setting->delete();

        return response()->json(['message' => 'Paramètre supprimé avec succès.']);
    }
}
