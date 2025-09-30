<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vending extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_uid',
        'item_id',
        'device',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function user()
    {
        return $this->belongsTo(UserItemLimit::class, 'user_uid', 'uid');
    }
}