<x-mail::message>
# Nuevo comprobante recibido

Hola,

Acaba de llegar un nuevo comprobante al buzón de Convelac:

**Código:** {{ $comprobante->codigo_envio }}  
**Tipo:** {{ ucfirst($comprobante->tipo) }}  
**Cliente:** {{ $comprobante->user->name }}  
**Fecha:** {{ $comprobante->fecha_envio->format('d/m/Y') }}  
**Archivos:** {{ count(json_decode($comprobante->archivos_json, true) ?? []) }}

<x-mail::button :url="url('/buzon-recepcion')">
Ir al Buzón ahora
</x-mail::button>

Saludos del sistema  
**Convelac**

</x-mail::message>