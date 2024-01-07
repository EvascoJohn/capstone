<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccess extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    private ?string $payment_amount, $payment_date, $payment_months_covered, $next_due;

    public function __construct($payment_amount, $payment_date, $payment_months_covered, $next_due)
    {
        $this->payment_amount = $payment_amount;
        $this->payment_date = $payment_date;
        $this->payment_months_covered = $payment_months_covered;
        $this->next_due = $next_due;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Success',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.payment.success',
            with: [
                'payment_amount' => $this->payment_amount,
                'payment_date' => $this->payment_date,
                'payment_months_covered' => $this->payment_months_covered,
                'next_due' => $this->next_due,
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
        return [];
    }
}
