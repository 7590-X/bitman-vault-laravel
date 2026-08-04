<?php

/**
 * Mailable de Laravel para la entrega de Notificaciones de Notas Seguras vía Brevo / SMTP.
 */

namespace App\Infrastructure\Mail;

use App\Domain\Entidades\EnvioNotaSegura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CorreoNotaSeguraMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly EnvioNotaSegura $nota,
        public readonly string $urlAcceso,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔒 Has recibido una Nota Segura en BITMAN Vault: ' . $this->nota->obtenerTitulo(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nota-segura',
            with: [
                'nota'      => $this->nota,
                'urlAcceso' => $this->urlAcceso,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
