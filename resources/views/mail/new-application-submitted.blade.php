<x-mail::message>
# New Application Received

A new application has been submitted on the Reality Venture website.

**Name:** {{ $application->first_name }} {{ $application->last_name }}

**Email:** {{ $application->email }}

**Program Interest:** {{ $application->program_interest->label() }}

@if($application->linkedin_profile)
**LinkedIn:** {{ $application->linkedin_profile }}
@endif

**Description:**

{{ $application->description }}

<x-mail::button :url="config('app.url') . '/admin/applications'">
View in Admin Panel
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
