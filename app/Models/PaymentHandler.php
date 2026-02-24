<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHandler extends Model
{
    protected $table = 'payment_handlers';

    protected $fillable = [
        'archive_code',
        'file_path',
        'date',
        'n_of_tuteurs_handled',
        'n_of_tuteurs_failed',
        'n_of_students_handled',
        'recipient_ccp',
    ];

    protected $casts = [
        'date' => 'date',
        'n_of_tuteurs_handled' => 'integer',
        'n_of_tuteurs_failed' => 'integer',
        'n_of_students_handled' => 'integer',
    ];
}
