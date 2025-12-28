<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\Request;

class EtablissementController extends Controller
{
    public function index()
    {
        return response()->json(Etablissement::with('commune')->get());
    }

    public function show($id)
    {
        $etablissement = Etablissement::with('commune')->find($id);
        return $etablissement ? response()->json($etablissement) : response()->json(['message' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_etabliss' => 'required|string|max:30|unique:etablissements,code_etabliss',
            'code_direction' => 'nullable|integer',
            'direction' => 'required|string|max:512',
            'nom_etabliss' => 'required|string|max:512',
            'code_commune' => 'nullable|string|exists:commune,code_comm',
            'commune' => 'required|string|max:512',
            'niveau_enseignement' => 'required|string|max:512',
            'adresse' => 'required|string|max:512',
            'nature_etablissement' => 'required|string|max:512',
        ]);

        $etab = Etablissement::create($validated);
        return response()->json($etab, 201);
    }

    public function update(Request $request, $id)
    {
        $etab = Etablissement::find($id);
        if (!$etab) return response()->json(['message' => 'Not found'], 404);

        $etab->update($request->all());
        return response()->json($etab);
    }

    public function destroy($id)
    {
        $etab = Etablissement::find($id);
        if (!$etab) return response()->json(['message' => 'Not found'], 404);

        $etab->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function getByFilters(Request $request)
    {
        $code_commune = $request->query('code_commune');
        $niveau = $request->query('niveau');
        $nature = $request->query('nature');

        // If no filters provided, return all (for backward compatibility)
        if (!$code_commune && !$niveau && !$nature) {
            return response()->json(Etablissement::with('commune')->get());
        }

        $query = Etablissement::query();

        if ($code_commune) {
            $query->where('code_commune', $code_commune);
        }

        if ($niveau) {
            $query->where('niveau_enseignement', $niveau);
        }

        if ($nature) {
            $query->where('nature_etablissement', $nature);
        }

        $etabs = $query->orderBy('nom_etabliss')->get();

        if ($etabs->isEmpty()) {
            return response()->json([]); // Return empty array instead of 404
        }

        return response()->json($etabs);
    }
}
