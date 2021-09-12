<?php

namespace App\Models;

use App\Traits\Models\UsesUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meditation extends Model
{
    use HasFactory;
    use UsesUUID;

    protected $fillable = [
        'title',
        'duration',
    ];
}
