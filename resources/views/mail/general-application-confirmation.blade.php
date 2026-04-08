<x-mail::message>
# Welcome to Reality Venture, {{ $application->first_name }}!

Thank you for submitting your application. Here is your reference ID:

<x-mail::panel>
**{{ $application->uid }}**
</x-mail::panel>

Keep this reference ID for your records. You can use it when contacting our team about your application.

---

## Your Application Summary

**Name:** {{ $application->first_name }} {{ $application->last_name }}

**Email:** {{ $application->email }}

@if($application->phone)
**Phone:** {{ $application->phone }}
@endif

@if($application->linkedin_profile)
**LinkedIn:** {{ $application->linkedin_profile }}
@endif

@if($application->program_interest)
**Program Interest:** {{ $application->program_interest->label() }}
@endif

@if($application->description)
**Description:**

{{ $application->description }}
@endif

---

## What Happens Next

Our team will review your application and get back to you. In the meantime, if you have a startup you would like to submit for consideration, you can apply through our startup application.

<x-mail::button :url="route('startup-application.form')">
Apply as a Startup
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
