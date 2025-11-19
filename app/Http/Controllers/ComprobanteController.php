<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudinary\Cloudinary;

class ComprobanteController extends Controller
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => 'densjsqxk',
                'api_key'    => '666357794399163',
                'api_secret' => 'jP0dBIcZmd9XRaYTdx3YtZ6X2ro',
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'archivo'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'fecha_envio' => 'required|date',
        ]);

        try {
            $result = $this->cloudinary->uploadApi()->upload(
                $request->file('archivo')->getRealPath(),
                [
                    'folder'         => 'comprobantes',
                    'upload_preset'  => 'laravel_unsigned', // ← este es el que creaste en Cloudinary
                    'resource_type'  => 'auto'
                ]
            );

            \App\Models\Comprobante::create([
                'user_id'      => auth()->id(),
                'url_archivo' => $result['secure_url'],  // o $url
                'public_id'    => $result['public_id'],
                'codigo_envio' => 'COMP-' . str_pad(\App\Models\Comprobante::count() + 1, 4, '0', STR_PAD_LEFT),
                'tipo'         => $request->has('es_retencion') ? 'retencion' : 'pago',
                'fecha_envio'  => $request->fecha_envio,
            ]);

            return back()->with('success', '¡Comprobante enviado con éxito!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}