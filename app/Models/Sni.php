<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sni extends Model
{
    protected $table = 'v2_setsni';
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];
}
