<?php

namespace App\Models;

use App\Enums\StatusClientUserEnum;
use Illuminate\Database\Eloquent\Model;

class ClientUser extends Model
{
    protected $fillable = [
        't_chat_id',
        'phone',
        'status',
    ];

    public function client_user_messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClientUserMessage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', StatusClientUserEnum::ACTIVE);
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', StatusClientUserEnum::BLOCKED);
    }
}
