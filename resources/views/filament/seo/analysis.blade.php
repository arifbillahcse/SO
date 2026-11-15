@php
  $checks = $record?->seo_checks['checks'] ?? [];
  $score = $record?->seo_score;
  $status = $record?->scoreStatus();
  $color = ['good' => '#16a34a', 'warning' => '#d97706', 'danger' => '#dc2626'][$status] ?? '#6b7280';
  $label = ['good' => 'Good', 'warning' => 'Needs improvement', 'danger' => 'Incomplete'][$status] ?? 'Not scanned yet';
  $icons = ['pass' => ['✓', '#16a34a'], 'warn' => ['!', '#d97706'], 'fail' => ['✗', '#dc2626'], 'na' => ['–', '#9ca3af']];
@endphp
<div style="display:grid;gap:16px">
  <div style="display:flex;flex-wrap:wrap;gap:16px;align-items:center;border:1px solid #e5e7eb;border-radius:12px;background:#fff;padding:16px">
    <div style="width:84px;height:84px;border-radius:50%;border:6px solid {{ $color }};display:flex;flex-direction:column;align-items:center;justify-content:center">
      <div style="font-size:24px;font-weight:800;color:{{ $color }};line-height:1">{{ $score ?? '—' }}</div>
      <div style="font-size:10px;color:#6b7280">/ 100</div>
    </div>
    <div style="flex:1;min-width:220px">
      <div style="font-size:16px;font-weight:700;color:{{ $color }}">{{ $label }}</div>
      <div style="font-size:13px;color:#4b5563;margin-top:2px">
        @if($record?->seo_scanned_at)
          Last scanned {{ $record->seo_scanned_at->diffForHumans() }}, from the page's rendered HTML. Saving re-scans automatically.
        @else
          Save the page or use <strong>Rescan</strong> to analyse it.
        @endif
      </div>
      <div style="font-size:12px;color:#6b7280;margin-top:6px">Internal completeness checklist (10 checks × 10 points; non-applicable checks excluded). It is not a Google score and does not predict rankings.</div>
    </div>
    <div style="font-size:13px;color:#374151;min-width:200px">
      <div><strong>Google index status:</strong> <span style="color:#6b7280">Not checked</span></div>
      <div><strong>Google performance:</strong> <span style="color:#6b7280">{{ $searchConnected ? 'Connected' : 'Search Console not connected' }}</span></div>
    </div>
  </div>

  @if($checks)
  <div style="border:1px solid #e5e7eb;border-radius:12px;background:#fff;overflow:hidden">
    @foreach($checks as $check)
      @php [$icon, $iconColor] = $icons[$check['status']] ?? $icons['na']; @endphp
      <div style="display:flex;gap:12px;padding:10px 14px;{{ ! $loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}">
        <div style="width:22px;height:22px;flex:none;border-radius:50%;background:{{ $iconColor }};color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center">{{ $icon }}</div>
        <div style="flex:1">
          <div style="font-size:13px;font-weight:600;color:#111827">{{ $check['label'] }} <span style="font-weight:400;color:#9ca3af">· {{ $check['status'] === 'na' ? 'n/a' : $check['points'].'/10' }}</span></div>
          <div style="font-size:13px;color:#4b5563">{{ $check['message'] }}</div>
        </div>
      </div>
    @endforeach
  </div>
  @endif
</div>
