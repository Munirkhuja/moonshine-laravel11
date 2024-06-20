<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientUserMessage extends Model
{
    protected $fillable = [
        't_chat_id',
        'client_user_id',
        'message',
    ];
}
