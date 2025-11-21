<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comprobante;
use Cloudinary\Cloudinary;

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

    // Guardar comprobantes
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
                'url'       => $result['url'],                    // URL pública (ya funciona porque activaste PDF delivery)
                'public_id' => $result['public_id'],
                'name'      => $archivo->getClientOriginalName(),
                'type'      => $archivo->getMimeType(),
            ];
        }

        Comprobante::create([
            'user_id'       => auth()->id(),
            'url_archivo'   => $archivosJson[0]['url'],
            'archivos_json' => json_encode($archivosJson),        // Siempre string JSON
            'archivos_vistos' => null,                            // nuevo campo, empieza vacío
            'codigo_envio'  => 'COMP-' . str_pad(Comprobante::count() + 1, 4, '0', STR_PAD_LEFT),
            'tipo'          => $request->has('es_retencion') ? 'retencion' : 'pago',
            'fecha_envio'   => $request->fecha_envio,
        ]);

        $cantidad = count($archivosJson);
        $mensaje = $cantidad == 1 
            ? "¡Se envió 1 archivo con éxito!" 
            : "¡Se enviaron {$cantidad} archivos con éxito!";

        return back()->with('success', $mensaje);
    }

    // NUEVO MÉTODO: marcar archivo como visto y abrirlo
    public function marcarVisto($comprobanteId, $publicId)
    {
        $comprobante = Comprobante::findOrFail($comprobanteId);

        // Cargar los ya vistos
        $vistos = $comprobante->archivos_vistos 
            ? json_decode($comprobante->archivos_vistos, true) 
            : [];

        // Si aún no está marcado, lo marcamos
        if (!in_array($publicId, $vistos)) {
            $vistos[] = $publicId;
            $comprobante->archivos_vistos = json_encode($vistos);
            $comprobante->save();
        }

        // Buscar la URL del archivo y redirigir para que se abra
        $archivos = json_decode($comprobante->archivos_json, true);
        foreach ($archivos as $archivo) {
            if ($archivo['public_id'] === $publicId) {
                return redirect()->to($archivo['url']);
            }
        }

        return back();
    }
}