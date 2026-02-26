<x-mail::message>
# Application Update

Dear {{ $name }},

We have reviewed your consultant application. Unfortunately, we are unable to approve your profile at this time.

@if($reason)
**Feedback:** {{ $reason }}
@endif

You are welcome to update your profile and reapply.

{{ config('app.name') }}
</x-mail::message>
