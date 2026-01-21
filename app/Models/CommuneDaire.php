<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommuneDaire extends Model
{
    protected $table = 'commune_daire';
    public $timestamps = false;
    // Note: This table has composite primary key (CW, CC), but Eloquent doesn't fully support it
    // We'll query by fields directly instead of using find()
    protected $fillable = [
        'CW',
        'CC',
        'WILAFR',
        'DAIRFR',
        'APCFR',
        'WILAAR',
        'DAIRAR',
        'APCAR',
    ];

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'CW', 'code_wil');
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class, 'CC', 'code_comm');
    }
}
