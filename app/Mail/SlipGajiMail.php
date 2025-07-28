<?php

namespace App\Mail;

use App\Models\GajiKaryawan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class SlipGajiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $gaji;
    public $pdfData;

    /**
     * Create a new message instance.
     */
    public function __construct(GajiKaryawan $gaji, $pdfData)
    {
        $this->gaji = $gaji;
        $this->pdfData = $pdfData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $periode = Carbon::parse($this->gaji->periode)->format('F Y');
        return new Envelope(
            subject: 'Slip Gaji Anda untuk Periode ' . $periode,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Kita perlu membuat file view ini
        return new Content(
            view: 'emails.slip-gaji',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $periode = Carbon::parse($this->gaji->periode)->format('F-Y');
        $namaFile = 'Slip Gaji - ' . $this->gaji->user->name . ' - ' . $periode . '.pdf';

        return [
            Attachment::fromData(fn () => $this->pdfData, $namaFile)
                ->withMime('application/pdf'),
        ];
    }
}