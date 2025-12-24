<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;

class EleveController extends Controller
{
    public function index()
    {
        return response()->json(
            Eleve::with(['tuteur', 'etablissement', 'commune'])->get()
        );
    }

    public function show($id)
    {
        $eleve = Eleve::with(['tuteur', 'etablissement', 'commune'])->find($id);
        return $eleve ? response()->json($eleve) : response()->json(['message' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        $tuteur = session('tuteur');

        if (!$tuteur || !isset($tuteur['nin'])) {
            return response()->json(['message' => 'Session invalide — tuteur manquant'], 403);
        }

        // 🔹 Step 1: Validate incoming form data
        $validated = $request->validate([
            'num_scolaire'   => 'required|string|max:16|unique:eleves,num_scolaire',
            'nom'            => 'required|string|max:50',
            'prenom'         => 'required|string|max:50',
            'nom_pere'       => 'required|string|max:50',
            'prenom_pere'    => 'required|string|max:50',
            'nom_mere'       => 'required|string|max:50',
            'prenom_mere'    => 'required|string|max:50',
            'date_naiss'     => 'nullable|date',
            'presume'        => 'nullable|string|in:0,1',
            'commune_naiss'  => 'nullable|string|max:5',
            'num_act'        => 'nullable|string|max:5',
            'bis'            => 'nullable|string|max:1',
            'ecole'          => 'nullable|string|max:30',
            'niveau'         => 'nullable|string|max:30',
            'classe_scol'    => 'nullable|string|max:30',
            'sexe'           => 'nullable|string|max:4',
            'handicap'       => 'nullable|string|in:0,1',
            'orphelin'       => 'nullable|string|in:0,1',
            'relation_tuteur'=> 'nullable|string|max:5',
            'nin_pere'       => 'nullable|string|max:18',
            'nin_mere'       => 'nullable|string|max:18',
            'nss_pere'       => 'nullable|string|max:12',
            'nss_mere'       => 'nullable|string|max:12',
        ]);

        // 🔹 Step 2: Map form field names → DB column names
        $data = [
            'num_scolaire'   => $validated['num_scolaire'],
            'nom'            => $validated['nom'],
            'prenom'         => $validated['prenom'],
            'nom_pere'       => $validated['nom_pere'],
            'prenom_pere'    => $validated['prenom_pere'],
            'nom_mere'       => $validated['nom_mere'],
            'prenom_mere'    => $validated['prenom_mere'],
            'date_naiss'     => $validated['date_naiss'] ?? null,
            'presume'        => $validated['presume'] ?? '0',
            'commune_naiss'  => $validated['commune_naiss'] ?? null,
            'num_act'        => $validated['num_act'] ?? null,
            'bis'            => $validated['bis'] ?? '0',
            'code_etabliss'  => $validated['ecole'] ?? null,
            'niv_scol'       => $validated['niveau'] ?? null,
            'classe_scol'    => $validated['classe_scol'] ?? null,
            'sexe'           => $validated['sexe'] ?? null,
            'handicap'       => $validated['handicap'] ?? '0',
            'orphelin'       => $validated['orphelin'] ?? '0',
            'relation_tuteur'=> $validated['relation_tuteur'] ?? null,
            'code_commune'   => $tuteur['code_commune']?? null,
            'nin_pere'       => $validated['nin_pere'] ?? null,
            'nin_mere'       => $validated['nin_mere'] ?? null,
            'nss_pere'       => $validated['nss_pere'] ?? null,
            'nss_mere'       => $validated['nss_mere'] ?? null,
            'etat_das'       => 'en_cours',
            'etat_final'     => 'en_cours',
            'dossier_depose' => 'non',
            'code_tuteur'    => $tuteur['nin'],
        ];

        // 🔹 Step 3: Insert student
        $eleve = Eleve::create($data);

        return response()->json($eleve, 201);
    }



    public function update(Request $request, $id)
    {
        $eleve = Eleve::find($id);
        if (!$eleve) return response()->json(['message' => 'Not found'], 404);

        $eleve->update($request->all());
        return response()->json($eleve);
    }

    public function destroy($id)
    {
        $eleve = Eleve::find($id);
        if (!$eleve) return response()->json(['message' => 'Not found'], 404);

        $eleve->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function byTuteur($nin)
    {
        $eleves = Eleve::where('code_tuteur', $nin)
            ->with(['etablissement', 'communeResidence', 'communeNaissance'])
            ->get();

        if ($eleves->isEmpty()) {
            return response()->json(['message' => 'Aucun enfant trouvé'], 404);
        }

        return response()->json($eleves);
    }

    public function checkMatricule($matricule)
    {
        $exists = Eleve::where('num_scolaire', $matricule)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Matricule déjà utilisé' : 'Matricule disponible'
        ]);
    }

    public function viewIstimara($num_scolaire)
    {
        $eleve = Eleve::with([
            'tuteur.communeResidence.wilaya',  // ✅ residence
            'tuteur.communeNaissance.wilaya',  // ✅ birth
            'etablissement.commune.wilaya',
            'communeResidence.wilaya',
            'communeNaissance.wilaya'
        ])
        ->where('num_scolaire', $num_scolaire)
        ->firstOrFail();

        $html = view('pdf.istimara', compact('eleve'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Amiri',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_direction' => 'rtl'
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output("استمارة_{$eleve->nom}_{$eleve->prenom}.pdf", 'I'))
            ->header('Content-Type', 'application/pdf');
    }


    public function downloadIstimara($num_scolaire)
    {
        $eleve = Eleve::with([
            'tuteur.communeResidence.wilaya',
            'tuteur.communeNaissance.wilaya',
            'etablissement.commune.wilaya',
            'communeResidence.wilaya',
            'communeNaissance.wilaya'
        ])
        ->where('num_scolaire', $num_scolaire)
        ->firstOrFail();

        $html = view('pdf.istimara', compact('eleve'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Amiri',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_direction' => 'rtl'
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output("استمارة_{$eleve->nom}_{$eleve->prenom}.pdf", 'D'))
            ->header('Content-Type', 'application/pdf');
    }


}
