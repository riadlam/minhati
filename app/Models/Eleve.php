<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    protected $table = 'eleves';
    protected $primaryKey = 'num_scolaire';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'num_scolaire', 'nom', 'prenom', 'nom_pere', 'prenom_pere', 'nom_mere', 'prenom_mere',
        'date_naiss', 'presume', 'commune_naiss', 'num_act', 'bis', 'code_etabliss',
        'niv_scol', 'classe_scol', 'sexe', 'handicap', 'handicap_nature', 'handicap_percentage',
        'orphelin', 'relation_tuteur', 'code_tuteur', 'code_commune', 'nin_pere', 'nin_mere',
        'nss_pere', 'nss_mere', 'etat_das', 'etat_final', 'dossier_depose',
        'approved_by', 'date_insertion', 'istimara'
    ];

    public function tuteur()
    {
        return $this->belongsTo(Tuteur::class, 'code_tuteur', 'nin');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_etabliss', 'code_etabliss');
    }

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

    // User who approved this eleve
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by', 'code_user');
    }

    // Comments on this eleve
    public function comments()
    {
        return $this->hasMany(Comment::class, 'eleve_id', 'num_scolaire');
    }
}
