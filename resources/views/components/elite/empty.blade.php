@props(['icon' => 'sparkle'])

<div {{ $attributes->class("elite-empty") }}>
    @switch($icon)
        @case('crown')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path stroke-linejoin="round" d="M3.5 18.5h17M4.5 18.5 3 7.5l5 4 4-6.5 4 6.5 5-4-1.5 11"/><circle cx="12" cy="4.5" r=".9" fill="currentColor"/></svg>
            @break
        @case('quill')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20 4c-7 0-12 5-14 14m0 0 3-1c6-2 9-6 11-13M4 20l2-2"/></svg>
            @break
        @default
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path stroke-linejoin="round" d="M12 3l1.8 5.6L19.5 10l-5.7 1.4L12 17l-1.8-5.6L4.5 10l5.7-1.4L12 3Z"/></svg>
    @endswitch
    <span>{{ $slot }}</span>
</div>
