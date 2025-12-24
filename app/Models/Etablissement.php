<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    protected $table = 'etablissements';
    protected $primaryKey = 'code_etabliss';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'code_etabliss',
        'code_direction',
        'direction',
        'nom_etabliss',
        'code_commune',
        'nom_commune',
        'niveau_enseignement',
        'adresse',
        'nature_etablissement',
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class, 'code_commune', 'code_comm');
    }

    public function eleves()
    {
        return $this->hasMany(Eleve::class, 'code_etabliss', 'code_etabliss');
    }
}
