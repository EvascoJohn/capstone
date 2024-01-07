<x-mail::message>
    Dear {{ $customer }},

    I trust this message finds you well. We wanted to express our gratitude for your recent successful payment toward your motorcycle loan with {{ config('app.name') }}.
    
    Payment Details:
    - Payment Amount: {{ $payment_amount }}
    - Transaction Date: {{ $payment_date }}
    - Payment Method: Paymongo
    - Months Covered: {{ $payment_months_covered }}
    - Your new due date will be: {{ $next_due }}

    Your prompt and successful payment is greatly appreciated, and it contributes to a positive financing experience.
    
    Thank you for choosing {{ config('app.name') }}. We look forward to continuing to serve you and make your motorcycle ownership experience enjoyable.
    
    Best regards,
    {{ config('app.name') }}
</x-mail::message>
