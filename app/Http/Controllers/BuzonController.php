<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BuzonController extends Controller
{
    // Buzón del admin: ve todos los comprobantes
    public function recepcion()
    {
        $comprobantes = Comprobante::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('buzon.recepcion', compact('comprobantes'));
    }

    // Buzón del cliente: ve solo los suyos
    public function envio()
    {
        $comprobantes = Comprobante::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('buzon.envio', compact('comprobantes'));
    }

    // Guardar el comprobante (subir a Cloudinary + guardar en BD)
    public function store(Request $request)
    {
        $request->validate([
            'fecha_envio' => 'required|date',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Subir a Cloudinary
        $uploadedFile = Cloudinary::upload($request->file('archivo')->getRealPath(), [
            'folder' => 'convelac/comprobantes',
        ])->getSecurePath();

        // Código automático COMP-0001, COMP-0002...
        $numero = Comprobante::max('id') + 1;
        $codigo = 'COMP-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        // Tipo según switch (si checked = retencion, sino pago)
        $tipo = $request->has('es_retencion') ? 'retencion' : 'pago';

        // Guardar en la tabla
        Comprobante::create([
            'user_id' => auth()->id(),
            'codigo_envio' => $codigo,
            'tipo' => $tipo,
            'url_archivo' => $uploadedFile,
            'fecha_envio' => $request->fecha_envio,
        ]);

        return back()->with('success', "Comprobante $codigo enviado con éxito");
    }
}