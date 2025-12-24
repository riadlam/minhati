<?php

namespace App\Http\Controllers;

use App\Models\Antenne;
use Illuminate\Http\Request;

class AntenneController extends Controller
{
    public function index()
    {
        return response()->json(Antenne::all());
    }

    public function show($id)
    {
        $antenne = Antenne::find($id);
        return $antenne ? response()->json($antenne) : response()->json(['message' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_ar' => 'required|string|max:2|unique:antennes,code_ar',
            'lib_ar_ar' => 'nullable|string|max:50',
            'lib_ar_fr' => 'nullable|string|max:50',
        ]);

        $antenne = Antenne::create($validated);
        return response()->json($antenne, 201);
    }

    public function update(Request $request, $id)
    {
        $antenne = Antenne::find($id);
        if (!$antenne) return response()->json(['message' => 'Not found'], 404);

        $antenne->update($request->all());
        return response()->json($antenne);
    }

    public function destroy($id)
    {
        $antenne = Antenne::find($id);
        if (!$antenne) return response()->json(['message' => 'Not found'], 404);

        $antenne->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
