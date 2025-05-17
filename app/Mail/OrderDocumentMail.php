<?php

namespace App\Mail;

use App\Models\OrderDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class OrderDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public OrderDocument $document
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $order = $this->document->order;
        $subject = match($this->document->type) {
            OrderDocument::TYPE_CREDENTIAL => 'Informasi Login untuk Pesanan #' . $order->order_number,
            OrderDocument::TYPE_DOMAIN => 'Informasi Domain untuk Pesanan #' . $order->order_number,
            OrderDocument::TYPE_UPDATE => 'Pembaruan untuk Pesanan #' . $order->order_number,
            OrderDocument::TYPE_DOWNLOAD => 'Link Unduhan untuk Pesanan #' . $order->order_number,
            default => 'Informasi Penting untuk Pesanan #' . $order->order_number,
        };
        
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-document',
            with: [
                'documentUrl' => route('orders.documents.show', [$this->document->order_id, $this->document->id]),
                'allDocumentsUrl' => route('my-documents'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->document->file_path) {
            $attachments[] = Attachment::fromStorage($this->document->file_path);
        }
        
        return $attachments;
    }
} 