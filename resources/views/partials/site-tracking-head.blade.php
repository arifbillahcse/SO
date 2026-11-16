@php
  $analyticsId = $orgSettings->googleAnalyticsId();
  $tagManagerId = $orgSettings->googleTagManagerId();
  $verificationToken = $orgSettings->googleVerificationToken();
@endphp
@if($verificationToken)
<meta name="google-site-verification" content="{{ $verificationToken }}" />
@endif
@if($analyticsId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $analyticsId }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', {{ Illuminate\Support\Js::from($analyticsId) }});
</script>
@endif
@if($tagManagerId)
<script>
  (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer',{{ Illuminate\Support\Js::from($tagManagerId) }});
</script>
@endif
@if($orgSettings->custom_code_enabled && filled($orgSettings->custom_head_code))
{!! $orgSettings->custom_head_code !!}
@endif
