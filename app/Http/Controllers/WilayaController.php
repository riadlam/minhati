<?php

namespace App\Http\Controllers;

use App\Models\Wilaya;
use Illuminate\Http\Request;

class WilayaController extends Controller
{
    public function index()
    {
        return response()->json(Wilaya::with('antenne')->get());
    }

    public function show($id)
    {
        $wilaya = Wilaya::with(['antenne', 'communes'])->find($id);
        return $wilaya ? response()->json($wilaya) : response()->json(['message' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_wil' => 'required|string|max:2|unique:wilaya,code_wil',
            'lib_wil_ar' => 'required|string|max:50',
            'lib_wil_fr' => 'required|string|max:50',
            'code_ar' => 'required|string|exists:antennes,code_ar',
        ]);

        $wilaya = Wilaya::create($validated);
        return response()->json($wilaya, 201);
    }

    public function update(Request $request, $id)
    {
        $wilaya = Wilaya::find($id);
        if (!$wilaya) return response()->json(['message' => 'Not found'], 404);

        $wilaya->update($request->all());
        return response()->json($wilaya);
    }

    public function destroy($id)
    {
        $wilaya = Wilaya::find($id);
        if (!$wilaya) return response()->json(['message' => 'Not found'], 404);

        $wilaya->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
