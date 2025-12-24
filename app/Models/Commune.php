<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $table = 'commune';
    protected $primaryKey = 'code_comm';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'code_comm',
        'lib_comm_ar',
        'lib_comm_fr',
        'code_wilaya',
    ];

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'code_wilaya', 'code_wil');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'code_comm', 'code_comm');
    }

    public function etablissements()
    {
        return $this->hasMany(Etablissement::class, 'code_commune', 'code_comm');
    }

    public function tuteurs()
    {
        return $this->hasMany(Tuteur::class, 'code_commune', 'code_comm');
    }

    public function eleves()
    {
        return $this->hasMany(Eleve::class, 'code_commune', 'code_comm');
    }
}
