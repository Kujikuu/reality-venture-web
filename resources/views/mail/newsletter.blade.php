<x-mail::message>
{!! $body !!}

<x-mail::button :url="config('app.url')">
Visit Our Website
</x-mail::button>

---

<small>You are receiving this email because you subscribed to our newsletter.
If you no longer wish to receive these emails, you can
<a href="{{ $unsubscribeUrl }}">unsubscribe here</a>.</small>
</x-mail::message>
