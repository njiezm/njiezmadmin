<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'reference',
        'date',
        'total_ht',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'total_ht' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(DevisItem::class);
    }
}