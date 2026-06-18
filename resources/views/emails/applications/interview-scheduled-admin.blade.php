<x-mail::message>
# Interview Scheduled

An interview has been scheduled for **{{ $application->company_name ?: $application->first_name }}** ({{ $application->uid }}).

**Applicant:** {{ $application->first_name }} {{ $application->last_name }}  
**Email:** {{ $application->email }}  
**Date & Time:** {{ $scheduledAt }}  
**Meeting Type:** {{ $meetingType }}

@if($meetingUrl)
**Google Meet:** [Join meeting]({{ $meetingUrl }})
@endif

@if($meetingLocation)
**Location:** {{ $meetingLocation }}
@endif

A calendar invite has been sent to you and the applicant via Google Calendar.

<x-mail::button :url="$adminUrl">
View Application
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
