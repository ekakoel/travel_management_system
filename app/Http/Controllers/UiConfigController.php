<?php

namespace App\Http\Controllers;

use App\Models\UiConfig;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUiConfigRequest;
use App\Http\Requests\UpdateUiConfigRequest;

class UiConfigController extends Controller
{
    public function index()
    {
        $configs = UiConfig::all();
        return view('admin.ui-config', compact('configs'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'page' => 'required|string',
                'name' => 'required|string|unique:ui_configs,name',
                'status' => 'required|boolean',
                'message' => 'nullable|string',
            ]);
    
            UiConfig::create([
                'page' => $request->page,
                'name' => $request->name,
                'status' => $request->status,
                'message' => $request->message ?? 'You are not permitted to access this page, 您無權存取該頁面',
            ]);
    
            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'error' => 'Nama sudah ada di database!'], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Terjadi kesalahan!'], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'old_name' => 'required|string',
                'name' => 'required|string|unique:ui_configs,name,' . $request->input('old_name') . ',name',
                'page' => 'required|string',
                'status' => 'required|boolean',
                'message' => 'required|string',
            ]);

            // Cek apakah name berubah
            $config = UiConfig::where('name', $request->input('old_name'))->first();

            if (!$config) {
                return response()->json(['success' => false, 'error' => 'Config not found'], 404);
            }

            // Update data
            $config->update([
                'name' => $request->input('name'),
                'page' => $request->input('page'),
                'status' => $request->input('status'),
                'message' => $request->input('message'),
            ]);

            return response()->json(['success' => true, 'data' => $config]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi khusus untuk name
            if ($e->errors()['name'] ?? false) {
                return response()->json(['success' => false, 'error' => 'Nama sudah ada di database!'], 422);
            }
            return response()->json(['success' => false, 'error' => 'Terjadi kesalahan validasi!'], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    
    public function toggle(Request $request)
    {
        $request->validate([
            'page' => 'required|string',
            'name' => 'required|string|exists:ui_configs,name',
            'status' => 'required|boolean',
            'message' => 'nullable|string',
        ]);

        // UiConfig::set($request->name, $request->status, $request->message);
        return response()->json(['success' => true]);
    }
    
   
}

