<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Father extends Model
{
    protected $table = 'fathers';
    public $timestamps = true;

    protected $fillable = [
        'nin', 'nss', 'nom_ar', 'prenom_ar', 'nom_fr', 'prenom_fr', 
        'categorie_sociale', 'montant_s', 'tuteur_nin', 'date_insertion',
        'biometric_id', 'biometric_id_back', 'Certificate_of_none_income', 
        'salary_certificate', 'Certificate_of_non_affiliation_to_social_security', 'crossed_ccp'
    ];

    protected $casts = [
        'montant_s' => 'decimal:2',
        'date_insertion' => 'date',
    ];

    // Relationship to Tuteur
    public function tuteur()
    {
        return $this->belongsTo(Tuteur::class, 'tuteur_nin', 'nin');
    }

    // Relationship to Eleves
    public function eleves()
    {
        return $this->hasMany(Eleve::class, 'father_id');
    }
}
