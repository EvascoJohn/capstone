<x-mail::message>
    {{ $title }}
    Dear {{ $customerName }},
    {{ $body }}
    {{ config('app.name') }}
</x-mail::message>