<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{

    use HasFactory;
    protected $fillable = [
        'device',
        'item_id',
        'limit',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    
}
