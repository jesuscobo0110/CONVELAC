<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use Illuminate\Http\Request;

class BuzonController extends Controller
{
    public function recepcion()
    {
        $comprobantes = Comprobante::all();  // Para admin: todos los comprobantes
        return view('buzon.recepcion', compact('comprobantes'));
    }

    public function envio()
    {
        $comprobantes = Comprobante::where('user_id', auth()->id())->get();  // Para cliente: solo los suyos
        return view('buzon.envio', compact('comprobantes'));
    }
}