<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = ['bet_id','player_name','provider','currency','game_id','wallet','bet_amount','winloss_amount','status','processed','bet_date','data'];

    protected $table = 'log';
}
