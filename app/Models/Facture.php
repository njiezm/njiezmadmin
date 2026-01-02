<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'reference',
        'date',
        'deadline',
        'tva_rate',
        'total_ht',
        'total_tva',
        'total_ttc',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'deadline' => 'date',
        'tva_rate' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'total_tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(FactureItem::class);
    }
}