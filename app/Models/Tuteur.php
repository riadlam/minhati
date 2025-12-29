<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tuteur extends Model
{
    protected $table = 'tuteures';
    protected $primaryKey = 'nin';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $hidden = ['password'];
    public $timestamps = false;

    protected $fillable = [
        'nin', 'nom_ar', 'prenom_ar', 'nom_fr', 'prenom_fr', 'date_naiss',
        'presume', 'commune_naiss', 'num_act', 'bis', 'sexe', 'nss',
        'adresse', 'num_cpt', 'cle_cpt', 'cats', 'montant_s', 'autr_info',
        'num_cni', 'date_cni', 'lieu_cni', 'tel', 'nbr_enfants_scolarise',
        'code_commune', 'date_insertion', 'email', 'password'
    ];

    // Commune of residence
    public function communeResidence()
    {
        return $this->belongsTo(Commune::class, 'code_commune', 'code_comm');
    }

    // Commune of birth
    public function communeNaissance()
    {
        return $this->belongsTo(Commune::class, 'commune_naiss', 'code_comm');
    }

    // Commune of CNI issuance (lieu_cni)
    public function communeCni()
    {
        return $this->belongsTo(Commune::class, 'lieu_cni', 'code_comm');
    }

    public function eleves()
    {
        return $this->hasMany(Eleve::class, 'code_tuteur', 'nin');
    }
}
