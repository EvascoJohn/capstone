<x-mail::message>

    Dear {{ $customer }},

    I hope this email finds you well. We appreciate the time and effort you invested in submitting your application to {{ config('app.name') }}. After careful consideration, we regret to inform you that your application has not been successful at this time.

    We understand that this news may be disappointing, and we want to assure you that the decision was not made lightly. We received a significant number of applications, and unfortunately, we had to make tough choices based on specific criteria.
    
    We encourage you to continue pursuing opportunities with {{ config('app.name') }} in the future, as your skills and qualifications are notable.
    
    Thank you for your interest in {{ config('app.name') }}, and we wish you the best in your future endeavors.
    
    Best regards,
{{ config('app.name') }}
</x-mail::message>
