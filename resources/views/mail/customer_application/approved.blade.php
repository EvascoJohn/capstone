<x-mail::message>
    Dear {{ $customer_name }},

    We are thrilled to inform you that your motorcycle financing application with {{ config('app.name') }} has been successfully approved! Congratulations on this exciting milestone!
    
    Details of Your Approval:
    - Unit: {{ $unit_name }}
    - Down Payment: {{ $downpayment }}
    - Monthly Payment: {{ $monthly }}
    - Terms: {{ $term }}
    
    We look forward to helping you ride off on the motorcycle of your dreams. Thank you for choosing {{ config('app.name') }}.
    
    Best regards,
{{ config('app.name') }}
</x-mail::message>
