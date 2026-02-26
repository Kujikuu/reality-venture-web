<x-mail::message>
# Congratulations, {{ $name }}!

Your consultant profile has been approved. You are now visible on the Reality Venture marketplace and can start receiving bookings.

<x-mail::button url="{{ url('/consultant/dashboard') }}">
Go to Dashboard
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
