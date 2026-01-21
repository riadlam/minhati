<?php

namespace App\Http\Controllers;

use App\Models\CommuneDaire;
use Illuminate\Http\Request;

class DairaController extends Controller
{
    public function getByWilaya($wilayaId)
    {
        // Get unique daira names for a specific wilaya
        $dairas = CommuneDaire::where('CW', $wilayaId)
            ->select('DAIRAR', 'DAIRFR', 'CW')
            ->distinct()
            ->orderBy('DAIRAR')
            ->get();

        if ($dairas->isEmpty()) {
            return response()->json([]);
        }

        return response()->json($dairas);
    }

    public function getByCommune($communeCode)
    {
        // Get daira for a specific commune
        // NOTE: commune codes may come as "0101" while commune_daire.CC might be stored as "101"
        $communeCode = (string) $communeCode;
        $trimmed = ltrim($communeCode, '0');
        $padded4 = str_pad($trimmed, 4, '0', STR_PAD_LEFT);

        $daira = CommuneDaire::whereIn('CC', array_values(array_unique([
                $communeCode,
                $trimmed,
                $padded4,
            ])))
            ->select('DAIRAR', 'DAIRFR', 'CW', 'CC')
            ->first();

        if (!$daira) {
            return response()->json([]);
        }

        return response()->json($daira);
    }
}
