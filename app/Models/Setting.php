<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['sender_ccp'];

    /**
     * Get the single settings row (create with defaults if missing).
     */
    public static function firstOrCreateDefault(): self
    {
        $row = self::first();
        if ($row) {
            return $row;
        }
        return self::create(['sender_ccp' => null]);
    }
}
