<?php

namespace BiiiiiigMonster\Remote\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'remote';

    protected $fillable = ['username', 'password', 'phone'];
}