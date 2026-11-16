@if($orgSettings->googleTagManagerId())
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $orgSettings->googleTagManagerId() }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
@endif
@if($orgSettings->custom_code_enabled && filled($orgSettings->custom_body_start_code))
{!! $orgSettings->custom_body_start_code !!}
@endif
