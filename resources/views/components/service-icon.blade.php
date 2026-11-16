@props(['group', 'icon' => null])
<svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($icon ?: $group)
        @case('code')
        @case('website-development')
            <rect x="3" y="4" width="18" height="16" rx="3"/><path d="M3 9h18m-12 3-3 3 3 3m6-6 3 3-3 3"/>
            @break
        @case('search')
        @case('seo-optimization')
            <circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5M7 12l3-3 2 2 2-3"/>
            @break
        @case('cloud')
        @case('managed-cloud')
            <path d="M6 19h12a4 4 0 0 0 .4-8 6.5 6.5 0 0 0-12.5-1.5A4.8 4.8 0 0 0 6 19Z"/><path d="m9 13 3-3 3 3m-3-3v6"/>
            @break
        @case('gear')
        @case('business-automation')
            <path d="M4 7h14m-3-3 3 3-3 3M20 17H6m3-3-3 3 3 3"/>
            @break
        @default
            <rect x="4" y="3" width="16" height="18" rx="3"/><path d="M12 7v6m-3-3h6M8 17h8"/>
    @endswitch
</svg>
