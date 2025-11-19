<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'codigo_envio',
        'tipo',
        'url_archivo',
        'fecha_envio',
    ];

    // Relación con el usuario (cliente) que lo envió
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
