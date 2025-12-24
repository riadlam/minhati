<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antenne extends Model
{
    protected $table = 'antennes';
    protected $primaryKey = 'code_ar';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'code_ar',
        'lib_ar_ar',
        'lib_ar_fr',
    ];

    // Relationships
    public function wilayas()
    {
        return $this->hasMany(Wilaya::class, 'code_ar', 'code_ar');
    }
}
