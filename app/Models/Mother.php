<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mother extends Model
{
    protected $table = 'mothers';
    public $timestamps = true;

    protected $fillable = [
        'nin', 'nss', 'nom_ar', 'prenom_ar', 'nom_fr', 'prenom_fr', 
        'categorie_sociale', 'montant_s', 'tuteur_nin', 'date_insertion'
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
        return $this->hasMany(Eleve::class, 'mother_id');
    }
}
