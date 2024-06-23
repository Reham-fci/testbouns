<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Salesperson extends Model
{
    protected $table = 'salesperson';
    protected $hidden = ['password','auth_token'];

    protected $casts = [
        'is_active'=>'integer'
    ];
}
