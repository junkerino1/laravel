<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserApi extends Model
{
    use HasFactory;

    protected $fillable = ['player_id', 'player_name'];

    protected $table = 'user_api';
}
