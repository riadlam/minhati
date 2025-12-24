<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use Illuminate\Http\Request;

class CommuneController extends Controller
{
    public function index()
    {
        return response()->json(Commune::with('wilaya')->get());
    }

    public function show($id)
    {
        $commune = Commune::with(['wilaya', 'users'])->find($id);
        return $commune ? response()->json($commune) : response()->json(['message' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_comm' => 'required|string|max:5|unique:commune,code_comm',
            'lib_comm_ar' => 'required|string|max:50',
            'lib_comm_fr' => 'required|string|max:50',
            'code_wilaya' => 'required|string|exists:wilaya,code_wil',
        ]);

        $commune = Commune::create($validated);
        return response()->json($commune, 201);
    }

    public function update(Request $request, $id)
    {
        $commune = Commune::find($id);
        if (!$commune) return response()->json(['message' => 'Not found'], 404);

        $commune->update($request->all());
        return response()->json($commune);
    }

    public function destroy($id)
    {
        $commune = Commune::find($id);
        if (!$commune) return response()->json(['message' => 'Not found'], 404);

        $commune->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function getByWilaya($wilayaId)
    {
        $communes = Commune::where('code_wilaya', $wilayaId)
            ->with('wilaya')
            ->get();

        if ($communes->isEmpty()) {
            return response()->json(['message' => 'No communes found for this wilaya'], 404);
        }

        return response()->json($communes);
    }

}
