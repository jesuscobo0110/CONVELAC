<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Comprobante;

class NuevoComprobanteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $comprobante;

    public function __construct(Comprobante $comprobante)
    {
        $this->comprobante = $comprobante;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo Comprobante - ' . $this->comprobante->codigo_envio,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.nuevo-comprobante',
        );
    }
}