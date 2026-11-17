@if($clientMapEnabled && count($clientCountries))
<section class="ab-section client-map" id="client-map" aria-labelledby="client-map-title">
  <div class="ab-wrap">
    <div class="client-map-heading">
      <div><span class="ab-eyebrow">Connected across borders</span><h2 class="ab-title" id="client-map-title">From Bangladesh.<br>Working worldwide.</h2><p>Different places. Shared ideas. Explore the countries and territories our clients call home.</p></div>
      <div class="client-map-total"><strong>{{ count($clientCountries) }}</strong><span>countries &amp; territories<br>worked with</span></div>
    </div>
    <div class="client-map-panel">
      <div class="client-map-legend"><span><i></i>Client locations</span><span><i class="client-map-home-dot"></i>Our home: Bangladesh</span></div>
      <svg class="client-map-world" viewBox="0 0 1000 510" aria-labelledby="client-world-title client-world-description">
        <title id="client-world-title">Our international client map</title>
        <desc id="client-world-description">Orange highlights show client locations. Hover, tap or use the country list below to explore them. Bangladesh is our home base.</desc>
        <g class="client-map-shapes">@include('partials.client-map-shapes')</g>
        <g class="client-map-home" transform="translate(734.14 213.01)" aria-hidden="true" pointer-events="none"><circle r="13" fill="var(--primary)" opacity=".18"/><circle r="5" fill="var(--text-primary)" stroke="var(--dark-2)" stroke-width="2"/></g>
      </svg>
      <div class="client-map-bottom"><p class="client-map-label" aria-live="polite" aria-atomic="true"><span class="client-map-code" data-map-code aria-hidden="true">BD</span><strong data-map-label>Bangladesh</strong><span data-map-description>Our home base</span></p><span class="client-map-hint">Hover or tap a highlighted location</span></div>
    </div>
    <details class="client-map-countries"><summary>Explore all {{ count($clientCountries) }} countries &amp; territories <span aria-hidden="true">+</span></summary><div class="client-map-country-list">@foreach($clientCountries as $code => $name)<button type="button" data-map-country="{{ $code }}" aria-pressed="false">{{ $name }}</button>@endforeach</div></details>
    <p class="client-map-note">Map boundaries are illustrative. Client locations include countries and territories.</p>
  </div>
</section>
<style>
.client-map{--map-accent:#f58220;--map-hover:#ffb46b;--map-land:#30303b;--map-boundary:#14141c}
html[data-theme="light"] .client-map{--map-accent:#e77513;--map-hover:#ffad60;--map-land:#dce1e9;--map-boundary:#fff}
.client-map-heading{display:flex;justify-content:space-between;align-items:center;gap:32px;margin-bottom:32px}
.client-map-heading .ab-title{margin:18px 0}
.client-map-heading p{max-width:540px;color:var(--text-secondary);font-size:.95rem;line-height:1.8}
.client-map-total{border:1px solid var(--border);border-radius:16px;background:var(--dark-2);padding:24px 30px;display:flex;align-items:center;gap:20px;flex-shrink:0}
.client-map-total strong{font-size:clamp(3.2rem,6vw,5rem);line-height:1;letter-spacing:-.06em;color:var(--primary);font-weight:700}
.client-map-total span{font-size:.8rem;line-height:1.7;color:var(--text-secondary)}
.client-map-panel{border:1px solid var(--border);border-radius:20px;background:var(--dark-2);overflow:hidden;box-shadow:0 12px 45px rgba(0,0,0,.035);background-image:radial-gradient(ellipse at 52% 45%,rgba(245,130,32,.07),transparent 65%)}
.client-map-legend{display:flex;gap:24px;padding:24px 28px 0;font-size:.76rem;color:var(--text-secondary);flex-wrap:wrap}
.client-map-legend span{display:flex;align-items:center;gap:8px}
.client-map-legend i{width:8px;height:8px;border-radius:50%;background:var(--map-accent)}
.client-map-legend .client-map-home-dot{background:var(--text-primary)}
.client-map-world{display:block;width:100%;height:auto;padding:12px 16px 0}
.client-map-shapes>*{fill:var(--map-land);stroke:var(--map-boundary);stroke-width:.55;stroke-linejoin:round;vector-effect:non-scaling-stroke}
@foreach($clientCountries as $code => $name)
#client-map [data-country="{{ $code }}"]{fill:var(--map-accent);cursor:pointer}
@endforeach
#client-map .client-map-shapes>.is-current{fill:var(--map-hover);stroke:var(--map-accent);stroke-width:.8}
.client-map-shapes>:focus-visible{outline:none;stroke:var(--text-primary)!important;stroke-width:1.4}
.client-map-code{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid var(--border);border-radius:8px;background:var(--dark-4);color:var(--primary);font-size:.65rem;font-weight:700;letter-spacing:.06em;flex-shrink:0}
.client-map-bottom{min-height:76px;background:var(--dark-2);border-top:1px solid var(--border);padding:18px 28px;display:flex;align-items:center;justify-content:space-between;gap:16px}
.client-map-label{margin:0;display:flex;align-items:center;gap:13px;flex-wrap:wrap}
.client-map-label strong{font-size:.88rem;color:var(--text-primary)}
.client-map-label [data-map-description]{font-size:.72rem;color:var(--text-muted)}
.client-map-hint{font-size:.74rem;color:var(--text-muted)}
.client-map-countries{margin-top:14px;border-bottom:1px solid var(--border)}
.client-map-countries summary{cursor:pointer;min-height:56px;align-items:center;padding:18px 0;color:var(--text-primary);font-weight:600;font-size:.82rem;list-style:none;display:flex;justify-content:space-between}
.client-map-countries summary::-webkit-details-marker{display:none}
.client-map-countries[open] summary>span{transform:rotate(45deg)}
.client-map-country-list{display:flex;flex-wrap:wrap;gap:8px;padding:0 0 22px}
.client-map-country-list button{font:inherit;font-size:.74rem;border:1px solid var(--border);border-radius:6px;min-height:44px;padding:10px 14px;background:var(--dark-2);color:var(--text-secondary);cursor:pointer}
.client-map-country-list button:hover,.client-map-country-list button[aria-pressed="true"]{border-color:var(--primary);color:var(--primary);background:var(--primary-glow)}
.client-map-country-list button:focus-visible,.client-map-countries summary:focus-visible{outline:2px solid var(--primary);outline-offset:3px}
.client-map-note{font-size:.7rem;line-height:1.7;color:var(--text-muted);margin-top:12px}
@media(max-width:640px){.client-map-heading{display:block}.client-map-total{display:inline-flex;margin-top:8px;padding:16px 20px;gap:16px;border-radius:12px}.client-map-total strong{font-size:3rem}.client-map-legend{padding:18px 16px 0;gap:14px;font-size:.7rem}.client-map-world{padding:14px 0 0}.client-map-bottom{padding:16px;align-items:flex-start;flex-direction:column;gap:10px}.client-map-hint{font-size:.7rem}.client-map-panel{border-radius:12px}}
</style>
<script>
(() => {
  const root = document.getElementById('client-map');
  if (!root) return;
  const label = root.querySelector('[data-map-label]');
  const description = root.querySelector('[data-map-description]');
  const countryCode = root.querySelector('[data-map-code]');
  const shapes = root.querySelector('.client-map-shapes');
  const countries = new Map(Array.from(root.querySelectorAll('[data-map-country]'), button => [button.dataset.mapCountry, button.textContent]));
  const select = code => {
    if (!countries.has(code)) return;
    shapes.querySelectorAll('.is-current').forEach(shape => shape.classList.remove('is-current'));
    shapes.querySelectorAll('[data-country="' + code + '"]').forEach(shape => shape.classList.add('is-current'));
    label.textContent = countries.get(code);
    countryCode.textContent = code;
    root.querySelectorAll('[data-map-country]').forEach(button => button.setAttribute('aria-pressed', String(button.dataset.mapCountry === code)));
    shapes.querySelectorAll('[role="button"]').forEach(shape => shape.setAttribute('aria-pressed', String(shape.dataset.country === code)));
    description.textContent = code === 'BD' ? 'Client location \u00b7 Our home base' : 'Client location';
  };
  shapes.querySelectorAll('[data-country]').forEach(shape => {
    if (!countries.has(shape.dataset.country)) return;
    shape.setAttribute('tabindex', '0');
    shape.setAttribute('role', 'button');
    shape.setAttribute('aria-pressed', 'false');
    shape.setAttribute('aria-label', countries.get(shape.dataset.country) + ', client location');
    shape.addEventListener('pointerenter', () => select(shape.dataset.country));
    shape.addEventListener('click', () => select(shape.dataset.country));
    shape.addEventListener('focus', () => select(shape.dataset.country));
    shape.addEventListener('keydown', event => {
      if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); select(shape.dataset.country); }
    });
  });
  root.querySelectorAll('[data-map-country]').forEach(button => {
    button.addEventListener('click', () => select(button.dataset.mapCountry));
  });
})();
</script>
@endif
