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
        $type_ecole = $request->query('type_ecole'); // Accept type_ecole from frontend

        // Map type_ecole to nature if nature is not provided
        if (!$nature && $type_ecole) {
            if ($type_ecole === 'متخصصة' || $type_ecole === 'متخصصة عمومية') {
                $nature = 'متخصصة';
            } elseif ($type_ecole === 'عمومية') {
                $nature = 'عمومية';
            }
        }

        \Log::info('EtablissementController::getByFilters', [
            'code_commune' => $code_commune,
            'niveau' => $niveau,
            'nature' => $nature,
            'type_ecole' => $type_ecole,
            'nature_length' => $nature ? strlen($nature) : 0,
            'nature_bytes' => $nature ? bin2hex($nature) : null,
        ]);

        // If no filters provided, return all (for backward compatibility)
        if (!$code_commune && !$niveau && !$nature) {
            return response()->json(Etablissement::with('commune')->get());
        }

        $query = Etablissement::query();

        if ($code_commune) {
            // Handle both "0101" and "101" formats (with/without leading zero)
            // Also handle cases where code_commune might be stored as integer or string
            $code_commune_trimmed = ltrim($code_commune, '0');
            $code_commune_padded = str_pad($code_commune_trimmed, 4, '0', STR_PAD_LEFT);
            
            \Log::info('EtablissementController::getByFilters - Commune code matching', [
                'original' => $code_commune,
                'trimmed' => $code_commune_trimmed,
                'padded' => $code_commune_padded,
            ]);
            
            $query->where(function($q) use ($code_commune, $code_commune_trimmed, $code_commune_padded) {
                $q->where('code_commune', $code_commune)
                  ->orWhere('code_commune', $code_commune_trimmed)
                  ->orWhere('code_commune', $code_commune_padded)
                  ->orWhereRaw('CAST(code_commune AS CHAR) = ?', [$code_commune])
                  ->orWhereRaw('CAST(code_commune AS CHAR) = ?', [$code_commune_trimmed])
                  ->orWhereRaw('CAST(code_commune AS CHAR) = ?', [$code_commune_padded]);
            });
        }

        // For specialized schools, skip niveau filter because they have different niveau_enseignement values
        // For regular schools, apply niveau filter
        if ($niveau && $nature !== 'متخصصة') {
            // niveau_enseignement can contain multiple values separated by commas
            // So we check if the niveau is contained in the field
            $query->where('niveau_enseignement', 'LIKE', '%' . $niveau . '%');
        }

        if ($nature) {
            // Log what we're searching for vs what exists in DB
            $sampleNatures = \DB::table('etablissements')
                ->select('nature_etablissement')
                ->distinct()
                ->limit(10)
                ->pluck('nature_etablissement');
            
            \Log::info('EtablissementController::getByFilters - Sample nature_etablissement values from DB', [
                'sample_natures' => $sampleNatures->toArray(),
                'searching_for' => $nature,
                'searching_for_hex' => bin2hex($nature),
            ]);
            
            // Try exact match first
            $query->where('nature_etablissement', $nature);
            
            // If no results, try trimming whitespace (in case DB has extra spaces)
            $testQuery = clone $query;
            $testResults = $testQuery->get();
            if ($testResults->isEmpty() && trim($nature) !== $nature) {
                \Log::info('EtablissementController::getByFilters - Trying trimmed value', [
                    'original' => $nature,
                    'trimmed' => trim($nature),
                ]);
                // Remove the previous where clause and add trimmed one
                $query = Etablissement::query();
                if ($code_commune) $query->where('code_commune', $code_commune);
                if ($niveau) $query->where('niveau_enseignement', $niveau);
                $query->whereRaw('TRIM(nature_etablissement) = ?', [trim($nature)]);
            }
        }

        $etabs = $query->orderBy('nom_etabliss')->get();

        // Debug: Check what schools exist with partial filters
        if ($etabs->isEmpty() && $code_commune && $niveau && $nature) {
            $debugCommune = \DB::table('etablissements')
                ->where('code_commune', $code_commune)
                ->count();
            $debugNiveau = \DB::table('etablissements')
                ->where('code_commune', $code_commune)
                ->where('niveau_enseignement', $niveau)
                ->count();
            $debugNature = \DB::table('etablissements')
                ->where('code_commune', $code_commune)
                ->where('nature_etablissement', $nature)
                ->count();
            $debugCommuneNiveau = \DB::table('etablissements')
                ->where('code_commune', $code_commune)
                ->where('niveau_enseignement', $niveau)
                ->where('nature_etablissement', $nature)
                ->count();
            $sampleSchools = \DB::table('etablissements')
                ->where('code_commune', $code_commune)
                ->select('nom_etabliss', 'niveau_enseignement', 'nature_etablissement')
                ->limit(5)
                ->get();
            
            // Check what commune codes actually exist (sample)
            $sampleCommuneCodes = \DB::table('etablissements')
                ->select('code_commune')
                ->distinct()
                ->limit(10)
                ->pluck('code_commune');
            
            // Check if there are any schools with similar commune code (maybe format issue)
            $similarCommune = \DB::table('etablissements')
                ->where('code_commune', 'LIKE', substr($code_commune, 0, 2) . '%')
                ->select('code_commune')
                ->distinct()
                ->limit(5)
                ->pluck('code_commune');
            
            \Log::info('EtablissementController::getByFilters - Debug breakdown', [
                'total_in_commune' => $debugCommune,
                'commune_and_niveau' => $debugNiveau,
                'commune_and_nature' => $debugNature,
                'commune_and_niveau_and_nature' => $debugCommuneNiveau,
                'sample_schools_in_commune' => $sampleSchools->toArray(),
                'searched_commune_code' => $code_commune,
                'sample_commune_codes_in_db' => $sampleCommuneCodes->toArray(),
                'similar_commune_codes' => $similarCommune->toArray(),
            ]);
        }

        \Log::info('EtablissementController::getByFilters - Results', [
            'count' => $etabs->count(),
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'first_school' => $etabs->first() ? [
                'code_etabliss' => $etabs->first()->code_etabliss,
                'nom_etabliss' => $etabs->first()->nom_etabliss,
                'code_commune' => $etabs->first()->code_commune,
                'nature_etablissement' => $etabs->first()->nature_etablissement,
            ] : null,
        ]);

        if ($etabs->isEmpty()) {
            // Log why no results were found
            if ($code_commune && $nature) {
                $totalInCommune = \DB::table('etablissements')
                    ->where(function($q) use ($code_commune) {
                        $code_commune_trimmed = ltrim($code_commune, '0');
                        $code_commune_padded = str_pad($code_commune_trimmed, 4, '0', STR_PAD_LEFT);
                        $q->where('code_commune', $code_commune)
                          ->orWhere('code_commune', $code_commune_trimmed)
                          ->orWhere('code_commune', $code_commune_padded);
                    })
                    ->count();
                $totalWithNature = \DB::table('etablissements')
                    ->where(function($q) use ($code_commune) {
                        $code_commune_trimmed = ltrim($code_commune, '0');
                        $code_commune_padded = str_pad($code_commune_trimmed, 4, '0', STR_PAD_LEFT);
                        $q->where('code_commune', $code_commune)
                          ->orWhere('code_commune', $code_commune_trimmed)
                          ->orWhere('code_commune', $code_commune_padded);
                    })
                    ->where('nature_etablissement', $nature)
                    ->count();
                \Log::info('EtablissementController::getByFilters - No results debug', [
                    'total_in_commune' => $totalInCommune,
                    'total_with_nature_in_commune' => $totalWithNature,
                ]);
            }
            return response()->json([]); // Return empty array instead of 404
        }

        return response()->json($etabs);
    }
}
