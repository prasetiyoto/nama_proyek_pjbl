<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shuttle extends Model
{
    protected $table = 'shuttles';

    protected $fillable = [
        'nama_armada',
        'kapasitas',
        'rute_operasional',
        'status_aktif',
    ];
    

    
}
