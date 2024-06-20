<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientRegister extends Model
{
    protected $fillable = [
        't_chat_id',
        'phone',
        'code',
        'count',
    ];
}
