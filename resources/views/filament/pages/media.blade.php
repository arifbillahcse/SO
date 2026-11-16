<x-filament-panels::page>
  <style>
    .media-library{--media-surface:#fff;--media-muted:#f8fafc;--media-border:#e5e7eb;--media-text:#111827;--media-secondary:#64748b;color:var(--media-text)}
    .dark .media-library{--media-surface:#18181b;--media-muted:#09090b;--media-border:#3f3f46;--media-text:#fafafa;--media-secondary:#a1a1aa}
    .media-intro{color:var(--media-secondary);font-size:.9rem;line-height:1.8;max-width:800px}
    .media-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin:24px 0}
    .media-stat{padding:20px;border:1px solid var(--media-border);border-radius:14px;background:var(--media-surface)}
    .media-stat strong{display:block;font-size:1.8rem;line-height:1.3}
    .media-stat span{font-size:.8rem;color:var(--media-secondary)}
    .media-toolbar{display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1fr) minmax(0,1fr) auto;gap:14px;align-items:end;margin:26px 0}
    .media-toolbar label{display:flex;flex-direction:column;gap:7px;font-size:.8rem;font-weight:600}
    .media-toolbar input,.media-toolbar select,.media-url{width:100%;min-width:0;color:var(--media-text);background:var(--media-surface);border:1px solid var(--media-border);border-radius:9px;padding:10px 12px;font-size:.85rem}
    .media-button{border:1px solid var(--media-border);background:var(--media-surface);border-radius:9px;padding:10px 14px;font-size:.8rem;font-weight:600;color:var(--media-text);cursor:pointer;white-space:nowrap}
    .media-button:hover{border-color:#ff7900;color:#d55d00}
    .media-button:disabled{opacity:.45;cursor:default}
    .media-library a:focus-visible,.media-library button:focus-visible,.media-library input:focus-visible,.media-library select:focus-visible,.media-library summary:focus-visible{outline:2px solid #ff7900;outline-offset:3px}
    .media-count{font-size:.82rem;color:var(--media-secondary);margin-bottom:18px}
    .media-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}
    .media-card{min-width:0;overflow:hidden;border:1px solid var(--media-border);border-radius:16px;background:var(--media-surface)}
    .media-preview{display:flex;align-items:center;justify-content:center;height:185px;background:var(--media-muted);border-bottom:1px solid var(--media-border);padding:16px;position:relative}
    .media-preview img{max-width:100%;max-height:100%;object-fit:contain}
    .media-missing{font-size:.85rem;color:#b45309}
    .media-card-content{padding:20px}
    .media-card h2{font-size:.88rem;font-weight:650;line-height:1.6;overflow-wrap:anywhere;margin-bottom:8px}
    .media-tags{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
    .media-tag{font-size:.65rem;padding:3px 8px;border-radius:100px;background:var(--media-muted);border:1px solid var(--media-border);color:var(--media-secondary)}
    .media-tag-warn{background:#fff7ed;color:#9a3412;border-color:#fed7aa}
    .media-info{font-size:.74rem;color:var(--media-secondary);line-height:1.8;margin-bottom:14px}
    .media-card details{border-top:1px solid var(--media-border);padding-top:14px;margin:18px 0}
    .media-card summary{font-size:.8rem;font-weight:600;cursor:pointer;color:var(--media-text)}
    .media-usage-list{list-style:none;padding:0;margin:12px 0 0;font-size:.76rem;line-height:1.7}
    .media-usage-list li{padding:7px 0;overflow-wrap:anywhere}
    .media-usage-list a{color:#c65300;text-decoration:underline;text-underline-offset:3px}
    .dark .media-usage-list a{color:#ff9b40}
    .media-unreferenced{font-size:.75rem;line-height:1.7;color:var(--media-secondary);margin:16px 0}
    .media-url-label{display:block;font-size:.7rem;color:var(--media-secondary);margin-bottom:5px}
    .media-url{font-size:.72rem;cursor:text}
    .media-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:12px}
    .media-actions a{font-size:.78rem;font-weight:600;color:#c65300}
    .dark .media-actions a{color:#ff9b40}
    .media-pagination{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-top:28px;font-size:.8rem}
    .media-empty{padding:48px 24px;text-align:center;border:1px dashed var(--media-border);border-radius:16px;color:var(--media-secondary)}
    @media(max-width:1100px){.media-toolbar{grid-template-columns:1fr 1fr}.media-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:640px){.media-grid,.media-toolbar{grid-template-columns:1fr}.media-stats{gap:8px}.media-stat{padding:12px}.media-stat strong{font-size:1.4rem}.media-stat span{font-size:.7rem}.media-pagination{flex-wrap:wrap}}
  </style>

  <div class="media-library">
    <p class="media-intro">Browse images from public uploads, website assets and saved content. Usage includes drafts, SEO settings and website source references. It is not a live crawl. Manage an image through its linked article, portfolio item or settings page.</p>
    <div class="media-stats">
      <div class="media-stat"><strong>{{ $totalImages }}</strong><span>Images in library</span></div>
      <div class="media-stat"><strong>{{ $referencedImages }}</strong><span>With saved references</span></div>
      <div class="media-stat"><strong>{{ $missingImages }}</strong><span>Missing local files</span></div>
    </div>

    <div class="media-toolbar">
      <label for="media-search">Search images or usage<input id="media-search" type="search" wire:model.live.debounce.350ms="search" placeholder="Filename, path, article or project"></label>
      <label for="media-source">Image source<select id="media-source" wire:model.live="source"><option value="all">All sources</option><option value="uploads">Public uploads</option><option value="assets">Website assets</option><option value="external">External images</option></select></label>
      <label for="media-usage">Usage<select id="media-usage" wire:model.live="usage"><option value="all">All images</option><option value="referenced">Has saved references</option><option value="unreferenced">No references found</option><option value="missing">Missing local files</option></select></label>
      <button type="button" class="media-button" wire:click="$refresh">Refresh library</button>
    </div>

    <p class="media-count" aria-live="polite">{{ $images->total() }} images match your filters. <span wire:loading.delay>Updating?</span></p>
    <div class="media-grid">
      @foreach($images as $image)
      <article class="media-card" wire:key="media-{{ sha1($image['key']) }}" x-data="{ copied: false }">
        <div class="media-preview">
          @if($image['exists'] !== false)<img src="{{ $image['url'] }}" alt="Preview of {{ $image['name'] }}" loading="lazy" decoding="async" referrerpolicy="no-referrer">@else<span class="media-missing">Image file not found</span>@endif
        </div>
        <div class="media-card-content">
          <h2>{{ $image['name'] }}</h2>
          <div class="media-tags"><span class="media-tag">{{ ['uploads' => 'Public upload', 'assets' => 'Website asset', 'external' => 'External image'][$image['source']] }}</span>@if($image['exists'] === false)<span class="media-tag media-tag-warn">Missing file</span>@endif</div>
          <p class="media-info">{{ $image['dimensions'] ?: 'Dimensions unavailable' }} &middot; {{ $image['size'] === null ? 'Size unavailable' : ($image['size'] >= 1048576 ? number_format($image['size'] / 1048576, 1).' MB' : number_format($image['size'] / 1024, 1).' KB') }}@if($image['source'] === 'external')<br>Hosted elsewhere; availability is not checked.@endif</p>

          @if(count($image['uses']))
          <details><summary>Used / referenced in {{ count($image['uses']) }} {{ count($image['uses']) === 1 ? 'place' : 'places' }}</summary><ul class="media-usage-list">@foreach($image['uses'] as $use)<li>@if($use['url'])<a href="{{ $use['url'] }}">{{ $use['label'] }}</a>@else{{ $use['label'] }}@endif</li>@endforeach</ul></details>
          @else
          <p class="media-unreferenced">No saved references found. This does not establish that the image is safe to delete.</p>
          @endif

          <label class="media-url-label" for="media-url-{{ sha1($image['key']) }}">Image URL</label>
          <input id="media-url-{{ sha1($image['key']) }}" x-ref="mediaUrl" class="media-url" value="{{ $image['url'] }}" readonly x-on:click="$el.select()">
          <div class="media-actions">
            <button type="button" class="media-button" x-on:click="if (navigator.clipboard) { navigator.clipboard.writeText($refs.mediaUrl.value).then(() => { copied = true; setTimeout(() => copied = false, 2000) }).catch(() => $refs.mediaUrl.select()) } else { $refs.mediaUrl.select() }" x-text="copied ? 'Copied!' : 'Copy URL'">Copy URL</button>
            @if($image['exists'] !== false)<a href="{{ $image['url'] }}" target="_blank" rel="noopener noreferrer">Open image &nearr;</a>@endif
          </div>
        </div>
      </article>
      @endforeach
    </div>
    @if($images->isEmpty())<p class="media-empty">No images match these filters. Try another search or image source.</p>@endif

    @if($images->hasPages())
    <div class="media-pagination"><button type="button" class="media-button" wire:click="previousPage" @disabled($images->onFirstPage())>Previous</button><span>Page {{ $images->currentPage() }} of {{ $images->lastPage() }}</span><button type="button" class="media-button" wire:click="nextPage" @disabled(!$images->hasMorePages())>Next</button></div>
    @endif
  </div>
</x-filament-panels::page>
