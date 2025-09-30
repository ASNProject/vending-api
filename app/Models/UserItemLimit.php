<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserItemLimit extends Model
{
    use HasFactory;
    protected $fillable = [
        'uid',
        'name',
        'role',
        'limit'
    ];
}
