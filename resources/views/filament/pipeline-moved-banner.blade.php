@php
    $movedAt = config('services.sniperhub.pipeline_moved_at');
    $date = filled($movedAt) ? \Illuminate\Support\Carbon::parse($movedAt)->translatedFormat('j F Y') : null;
@endphp

<div
    role="status"
    style="margin-bottom: 1rem; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid rgb(6 45 45 / 0.25); background: rgb(6 45 45 / 0.06); font-size: 0.875rem; line-height: 1.5;"
>
    <strong>{{ $date ? "Applications moved to Sniper Hub on {$date}." : 'Applications moved to Sniper Hub.' }}</strong>
    The list here is read-only. Move, schedule and decide applications in Sniper Hub.
</div>
