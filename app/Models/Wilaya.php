<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilaya extends Model
{
    protected $table = 'wilaya';
    protected $primaryKey = 'code_wil';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'code_wil',
        'lib_wil_ar',
        'lib_wil_fr',
        'code_ar',
    ];

    public function antenne()
    {
        return $this->belongsTo(Antenne::class, 'code_ar', 'code_ar');
    }

    public function communes()
    {
        return $this->hasMany(Commune::class, 'code_wilaya', 'code_wil');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'code_wilaya', 'code_wil');
    }
}
