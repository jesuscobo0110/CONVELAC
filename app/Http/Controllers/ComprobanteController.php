<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comprobante;
use Cloudinary\Cloudinary;

class ComprobanteController extends Controller
{
 public function store(Request $request)
{
    $request->validate([
        'archivos'   => 'required|array|min:1|max:10',
        'archivos.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        'fecha_envio' => 'required|date',
    ]);

    $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
    $archivosJson = [];

    foreach ($request->file('archivos') as $archivo) {
        $result = $cloudinary->uploadApi()->upload($archivo->getRealPath(), [
            'folder' => 'comprobantes',
            'resource_type' => 'auto',
        ]);

        $archivosJson[] = [
            'url'       => $result['secure_url'],
            'public_id' => $result['public_id'],
            'name'      => $archivo->getClientOriginalName(),
            'type'      => $archivo->getMimeType(),
        ];
    }

    Comprobante::create([
        'user_id'       => auth()->id(),
        'url_archivo'   => $archivosJson[0]['url'],
        'archivos_json' => $archivosJson,
        'codigo_envio'  => 'COMP-' . str_pad(Comprobante::count() + 1, 4, '0', STR_PAD_LEFT),
        'tipo'          => $request->has('es_retencion') ? 'retencion' : 'pago',
        'fecha_envio'   => $request->fecha_envio,
    ]);

    // ← MENSAJE CORREGIDO AQUÍ
    $cantidad = count($archivosJson);
    $mensaje = $cantidad == 1 
        ? "¡Se envió 1 archivo con éxito!" 
        : "¡Se enviaron {$cantidad} archivos con éxito!";

    return back()->with('success', $mensaje);
}
}