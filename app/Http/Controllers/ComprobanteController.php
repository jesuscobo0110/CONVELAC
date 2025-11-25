<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comprobante;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Mail;           // ← NUEVO
use App\Mail\NuevoComprobanteMail;             // ← NUEVO

class ComprobanteController extends Controller
{
    // Página de envío (cliente)
    public function envio()
    {
        return view('buzon.envio');
    }

    // Página de recepción (admin)
    public function recepcion()
    {
        $comprobantes = Comprobante::with('user')->latest()->paginate(12);
        return view('buzon.recepcion', compact('comprobantes'));
    }

    // Guardar comprobantes + ENVIAR EMAIL AUTOMÁTICO
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

        // CREAR EL COMPROBANTE
        $comprobante = Comprobante::create([
            'user_id'         => auth()->id(),
            'url_archivo'     => $archivosJson[0]['url'],
            'archivos_json'   => json_encode($archivosJson),
            'archivos_vistos' => null,
            'codigo_envio'    => 'COMP-' . str_pad(Comprobante::count() + 1, 4, '0', STR_PAD_LEFT),
            'tipo'            => $request->has('es_retencion') ? 'retencion' : 'pago',
            'fecha_envio'     => $request->fecha_envio,
        ]);

        // ENVIAR CORREO AUTOMÁTICO AL ADMIN
        try {
            Mail::to('jesuscobo0110@gmail.com') // ← cambia por el correo del admin cuando quieras
                ->send(new NuevoComprobanteMail($comprobante));
        } catch (\Exception $e) {
            // Si falla el email, no rompemos todo (solo se guarda en logs)
            \Log::warning('No se pudo enviar email de nuevo comprobante: ' . $e->getMessage());
        }

        $cantidad = count($archivosJson);
        $mensaje = $cantidad == 1 
            ? "¡Se envió 1 archivo con éxito!" 
            : "¡Se enviaron {$cantidad} archivos con éxito!";

        return back()->with('success', $mensaje);
    }

        // Marcar como visto (AJAX - para el botón VER)
    public function marcarVistoAjax($comprobanteId, $publicId)
    {
        $comprobante = \App\Models\Comprobante::findOrFail($comprobanteId);
        $vistos = $comprobante->archivos_vistos ? json_decode($comprobante->archivos_vistos, true) : [];
    
        if (!in_array($publicId, $vistos)) {
            $vistos[] = $publicId;
            $comprobante->archivos_vistos = json_encode($vistos);
            $comprobante->save();
        }

        return response()->json(['success' => true]);
    }

    // Descargar con nombre original (por si lo vuelves a activar algún día)
    public function download($filename, Request $request)
    {
        $original_name = $request->query('original_name', $filename);

        $url = "https://res.cloudinary.com/densjsqxk/raw/upload/v1/{$filename}";

        $response = \Illuminate\Support\Facades\Http::get($url);

        if ($response->failed()) {
            $url = "https://res.cloudinary.com/densjsqxk/raw/upload/v1/" . basename($filename);
            $response = \Illuminate\Support\Facades\Http::get($url);
            if ($response->failed()) {
                abort(404);
            }
        }

        return response($response->body(), 200)
            ->header('Content-Type', $response->header('Content-Type') ?? 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $original_name . '"');
    }
}