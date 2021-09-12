<?php

namespace App\Models;

use App\Traits\Models\UsesUUID;
use Illuminate\Database\Eloquent\Model;

class UserMeditation extends Model
{
    use UsesUUID;

    protected $fillable = [
        'user_id',
        'meditation_id',
    ];
}
