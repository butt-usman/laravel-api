<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW. 
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill',
        'value'
    ];
    public $timestamps = false;
    public static $snakeAttributes = false;

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
