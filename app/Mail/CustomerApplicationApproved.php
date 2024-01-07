<?php

namespace App\Mail;

use App\Livewire\CustomerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

     private ?string $customer_name;
     private ?string $unit_name;
     private ?string $term;
     private ?string $monthly;

     private ?string $downpayment;
     

    public function __construct($customer_name, $unit_name, $term, $monthly, $downpayment)
    {
        $this->customer_name = $customer_name;
        $this->unit_name = $unit_name;
        $this->term = $term;
        $this->monthly = $monthly;
        $this->downpayment = $downpayment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Customer Application Approved',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.customer_application.approved',
            with: [
                'customer_name' => $this->customer_name,
                'unit_name' => $this->unit_name,
                'term' => $this->term,
                'monthly' => $this->monthly,
                'downpayment' => $this->downpayment,
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
