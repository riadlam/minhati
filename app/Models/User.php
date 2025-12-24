<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'code_user';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'code_user',
        'nom_user',
        'prenom_user',
        'pass',
        'fonction',
        'organisme',
        'statut',
        'code_comm',
        'code_wilaya',
        'role',
        'date_insertion',
    ];

    protected $hidden = [
        'pass',
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class, 'code_comm', 'code_comm');
    }

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'code_wilaya', 'code_wil');
    }
}
