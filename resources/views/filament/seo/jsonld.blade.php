@php
  $page = $record?->seo_checks['page'] ?? [];
  $blocks = $page['schema_blocks'] ?? [];
  $errors = $page['schema_errors'] ?? 0;
@endphp
<div style="border:1px solid #e5e7eb;border-radius:12px;background:#fff;padding:14px">
  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:10px">
    <div style="font-size:12px;font-weight:600;color:#6b7280">JSON-LD ON THE LIVE PAGE (FROM LAST SCAN)</div>
    @if($record?->seo_scanned_at)
      <div style="font-size:12px;font-weight:600;color:{{ $errors ? '#dc2626' : '#16a34a' }}">{{ $errors ? $errors.' invalid block(s)' : count($blocks).' valid block(s): '.implode(', ', array_map(fn ($b) => $b['@type'] ?? '?', $blocks)) }}</div>
    @endif
  </div>
  @if($blocks)
    <pre style="margin:0;max-height:360px;overflow:auto;background:#0f172a;color:#e2e8f0;border-radius:8px;padding:12px;font-size:12px;line-height:1.5">{{ json_encode($blocks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
    <div style="font-size:11px;color:#9ca3af;margin-top:8px">Syntax-checked here. For Google's eligibility check, paste the page URL into the Rich Results Test (search.google.com/test/rich-results).</div>
  @else
    <div style="font-size:13px;color:#6b7280">Not scanned yet — save or rescan to see the structured data this page outputs.</div>
  @endif
</div>
