@php
  $titleLen = mb_strlen($title);
  $descLen = mb_strlen($description);
  $crumb = rtrim(str_replace(['https://', 'http://'], '', $url), '/');
  $crumb = str_replace('/', ' › ', $crumb);
  $shownTitle = $titleLen > 60 ? mb_substr($title, 0, 58).'…' : $title;
  $shownDesc = $descLen > 160 ? mb_substr($description, 0, 157).'…' : $description;
  $ok = fn ($len, $min, $max) => $len >= $min && $len <= $max ? '#16a34a' : ($len === 0 ? '#dc2626' : '#d97706');
@endphp
<div style="border:1px solid #e5e7eb;border-radius:12px;background:#fff;padding:16px;font-family:arial,sans-serif">
  <div style="font-size:12px;font-weight:600;color:#6b7280;margin-bottom:10px;font-family:inherit">GOOGLE SEARCH PREVIEW</div>
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px">
    <div style="width:28px;height:28px;border-radius:50%;background:#fff3e6;border:1px solid #fed7aa;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#ff7900">S</div>
    <div style="line-height:1.3">
      <div style="font-size:14px;color:#202124">Softorio</div>
      <div style="font-size:12px;color:#4d5156">{{ $crumb }}</div>
    </div>
  </div>
  <div style="font-size:20px;line-height:1.3;color:#1a0dab;margin:6px 0 4px">{{ $shownTitle }}</div>
  <div style="font-size:14px;line-height:1.58;color:#4d5156">{{ $shownDesc ?: 'No description — Google will pick text from the page.' }}</div>
  <div style="margin-top:14px;padding-top:10px;border-top:1px solid #f3f4f6;font-size:12px;color:#374151;display:grid;gap:4px">
    <div>Title: <strong style="color:{{ $ok($titleLen, 50, 60) }}">{{ $titleLen }}</strong> / 60 chars @if($usingDefaultTitle)<span style="color:#9ca3af">(page default)</span>@endif</div>
    <div>Description: <strong style="color:{{ $ok($descLen, 140, 160) }}">{{ $descLen }}</strong> / 160 chars @if($usingDefaultDescription)<span style="color:#9ca3af">(page default)</span>@endif</div>
    @if(filled($keyword))
      <div>Keyword "{{ $keyword }}": in title {!! mb_stripos($title, $keyword) !== false ? '<span style="color:#16a34a">✓</span>' : '<span style="color:#dc2626">✗</span>' !!}, in description {!! mb_stripos($description, $keyword) !== false ? '<span style="color:#16a34a">✓</span>' : '<span style="color:#dc2626">✗</span>' !!}</div>
    @endif
  </div>
  <div style="margin-top:8px;font-size:11px;color:#9ca3af">Approximation only — Google may rewrite titles and choose its own snippet.</div>
</div>
