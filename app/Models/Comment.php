<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'user_id',
        'eleve_id',
        'text'
    ];

    // User who made the comment
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'code_user');
    }

    // Eleve this comment is about
    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'eleve_id', 'num_scolaire');
    }
}
