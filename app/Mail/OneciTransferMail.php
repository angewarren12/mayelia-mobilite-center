<?php

namespace App\Mail;

use App\Models\DossierOneciTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OneciTransferMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transfer;
    protected $pdf;

    /**
     * Create a new message instance.
     */
    public function __construct(DossierOneciTransfer $transfer, $pdf)
    {
        $this->transfer = $transfer;
        $this->pdf = $pdf;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Transfert de dossiers - ' . $this->transfer->code_transfert,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.oneci-transfer',
            with: [
                'transfer' => $this->transfer,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdf->output(), 'transfert-' . $this->transfer->code_transfert . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}


