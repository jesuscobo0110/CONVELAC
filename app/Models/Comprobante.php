<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url_archivo',
        'archivos_json',
        'codigo_envio',
        'tipo',
        'fecha_envio',
    ];

    protected $casts = [
        'archivos_json' => 'array',
        'fecha_envio'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}