<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatPerUser extends Model
{
    protected $table = 'chat_per_user';

    use HasFactory;
}
