<?php

namespace App\Models;

class UserModel extends BaseModel
{
    protected $table    = 'users';
    protected $fillable = [
        'id',
        'today',
        'limit',
        'telegram_id',
        'condition',
        'username',
        'promo',
        'ads',
        'time',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}