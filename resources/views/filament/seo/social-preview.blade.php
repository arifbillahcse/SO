<div style="display:grid;gap:16px;font-family:Helvetica,Arial,sans-serif">
  <div style="border:1px solid #e5e7eb;border-radius:12px;background:#fff;overflow:hidden">
    <div style="font-size:12px;font-weight:600;color:#6b7280;padding:12px 14px 8px">FACEBOOK / LINKEDIN PREVIEW</div>
    <div style="aspect-ratio:1.91/1;background:#f3f4f6 url('{{ $image }}') center/cover no-repeat"></div>
    <div style="background:#f0f2f5;padding:10px 12px;border-top:1px solid #e5e7eb">
      <div style="font-size:12px;color:#606770;text-transform:uppercase">{{ $domain }}</div>
      <div style="font-size:16px;font-weight:600;color:#1d2129;line-height:1.3;margin:2px 0">{{ \Illuminate\Support\Str::limit($title, 88) }}</div>
      <div style="font-size:14px;color:#606770;line-height:1.35">{{ \Illuminate\Support\Str::limit($description, 110) }}</div>
    </div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;background:#fff;overflow:hidden;padding-bottom:10px">
    <div style="font-size:12px;font-weight:600;color:#6b7280;padding:12px 14px 8px">X (TWITTER) PREVIEW</div>
    @if($twitterCard === 'summary')
      <div style="display:flex;margin:0 12px;border:1px solid #cfd9de;border-radius:14px;overflow:hidden">
        <div style="width:110px;min-height:110px;background:#f3f4f6 url('{{ $twitterImage }}') center/cover no-repeat"></div>
        <div style="padding:10px 12px;flex:1">
          <div style="font-size:13px;color:#536471">{{ $domain }}</div>
          <div style="font-size:15px;color:#0f1419;line-height:1.3">{{ \Illuminate\Support\Str::limit($twitterTitle, 70) }}</div>
          <div style="font-size:13px;color:#536471">{{ \Illuminate\Support\Str::limit($twitterDescription, 100) }}</div>
        </div>
      </div>
    @else
      <div style="margin:0 12px;border:1px solid #cfd9de;border-radius:14px;overflow:hidden;position:relative">
        <div style="aspect-ratio:1.91/1;background:#f3f4f6 url('{{ $twitterImage }}') center/cover no-repeat"></div>
        <div style="position:absolute;left:10px;bottom:10px;background:rgba(0,0,0,.77);color:#fff;font-size:13px;padding:2px 6px;border-radius:4px">{{ \Illuminate\Support\Str::limit($twitterTitle, 60) }}</div>
      </div>
      <div style="font-size:13px;color:#536471;margin:6px 14px 0">From {{ $domain }}</div>
    @endif
  </div>
  <div style="font-size:11px;color:#9ca3af">Approximate. Networks cache previews; use their debuggers to refresh after changes.</div>
</div>
