<div class="service-forge" data-service-forge data-paused="false">
  <svg class="forge-scene" viewBox="0 0 600 560" role="img" aria-labelledby="forge-title forge-description">
    <title id="forge-title">Softorio's connected digital services</title>
    <desc id="forge-description">An orange Softorio engine connects website development, cloud infrastructure, search visibility, automation and diagnostic software through flowing signal paths.</desc>
    <defs>
      <linearGradient id="forge-orange" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#FFC078"/><stop offset=".45" stop-color="#FF7900"/><stop offset="1" stop-color="#D44C00"/></linearGradient>
      <linearGradient id="forge-ribbon" x1="140" y1="190" x2="430" y2="320" gradientUnits="userSpaceOnUse"><stop stop-color="#FFB867"/><stop offset=".35" stop-color="#FF7900"/><stop offset=".7" stop-color="#F06600"/><stop offset="1" stop-color="#FFB867"/></linearGradient>
      <linearGradient id="forge-face" x1="0" y1="0" x2="1" y2="1"><stop class="forge-face-start"/><stop offset="1" class="forge-face-end"/></linearGradient>
      <radialGradient id="forge-aura"><stop stop-color="#FF7900" stop-opacity=".14"/><stop offset="1" stop-color="#FF7900" stop-opacity="0"/></radialGradient>
      <filter id="forge-shadow" x="-45%" y="-45%" width="190%" height="210%"><feDropShadow dx="0" dy="12" stdDeviation="14" flood-color="#2b1603" flood-opacity=".1"/></filter>
      <filter id="forge-glow" x="-80%" y="-80%" width="260%" height="260%"><feGaussianBlur stdDeviation="8"/></filter>
      <pattern id="forge-dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="1" cy="1" r=".8" fill="currentColor"/></pattern>
    </defs>

    <circle cx="300" cy="278" r="225" fill="url(#forge-aura)"/>
    <circle cx="300" cy="278" r="216" fill="url(#forge-dots)" class="forge-dot-field"/>
    <g class="forge-floor" fill="none" stroke-width="1">
      <path d="M85 366 300 490 515 366 300 242Z"/>
      <path d="m130 340 215 124m-172-149 215 124m-172-149 215 124m-172-149 215 124"/>
      <path d="m130 392 215-124m-172 149 215-124m-172 149 215-124m-172 149 215-124"/>
    </g>

    <g class="forge-connectors" fill="none" stroke-width="1.4">
      <path d="M170 193H204Q224 193 238 209L267 240"/>
      <path d="M300 120V182"/>
      <path d="M432 205H401Q386 205 375 218L344 250"/>
      <path d="M434 385 392 361Q374 350 352 350H323"/>
      <path d="M174 389 211 367Q231 354 253 354H278"/>
    </g>
    <g class="forge-signals" fill="none" stroke="#FF7900" stroke-width="2.8" stroke-linecap="round">
      <path d="M170 193H204Q224 193 238 209L267 240"/>
      <path d="M300 120V182" style="animation-delay:-1.1s"/>
      <path d="M432 205H401Q386 205 375 218L344 250" style="animation-delay:-2.3s"/>
      <path d="M434 385 392 361Q374 350 352 350H323" style="animation-delay:-3.2s"/>
      <path d="M174 389 211 367Q231 354 253 354H278" style="animation-delay:-.7s"/>
    </g>

    <ellipse cx="300" cy="419" rx="112" ry="22" class="forge-ground-shadow"/>
    <g class="forge-platform">
      <path d="m181 353 119 69 119-69v17l-119 69-119-69Z" class="forge-platform-side"/>
      <path d="m181 353 119-69 119 69-119 69Z" fill="url(#forge-face)" class="forge-outline"/>
      <path d="m199 342 101 58 101-58" fill="none" stroke="#FF7900" stroke-opacity=".45"/>
      <path d="m195 329 105-61 105 61-105 61Z" fill="url(#forge-face)" class="forge-outline"/>
      <path d="m218 329 82-47 82 47-82 47Z" class="forge-platform-inset"/>
      <path d="M300 304v44" stroke="#FF7900" stroke-width="2" stroke-dasharray="3 6" class="forge-core-signal"/>
      <ellipse cx="300" cy="329" rx="35" ry="17" fill="url(#forge-aura)" class="forge-core-pulse"/>
    </g>

    <g class="forge-core">
      <ellipse cx="300" cy="276" rx="124" ry="68" transform="rotate(-24 300 276)" fill="none" class="forge-orbit-back"/>
      <path d="M185 281C146 198 228 140 313 162c82 22 130 83 99 144" fill="none" stroke="url(#forge-ribbon)" stroke-width="9" stroke-linecap="round"/>
      <g class="forge-heart" filter="url(#forge-shadow)">
        <path d="m300 188 62 35v71l-62 36-62-36v-71Z" class="forge-glass-shell"/>
        <path d="m300 188 62 35-62 36-62-36Z" class="forge-glass-top"/>
        <path d="M300 259v71m-62-107 62 36 62-36" fill="none" class="forge-glass-edge"/>
        <rect x="260" y="214" width="80" height="80" rx="23" fill="url(#forge-orange)"/>
        <path d="M277 218h35" stroke="#FFF1DE" stroke-opacity=".65" stroke-width="2" stroke-linecap="round"/>
        <image href="{{ asset('images/logo-icon.png') }}" x="274" y="228" width="52" height="52"/>
      </g>
      <path d="M412 306c-27 65-133 78-192 33-23-17-34-37-35-58" fill="none" stroke="url(#forge-ribbon)" stroke-width="9" stroke-linecap="round"/>
      <path d="M412 306c-27 65-133 78-192 33-23-17-34-37-35-58" fill="none" stroke="#FFE3BC" stroke-opacity=".6" stroke-width="1.4" stroke-linecap="round"/>
      <ellipse cx="300" cy="276" rx="139" ry="80" transform="rotate(24 300 276)" fill="none" class="forge-orbit-fine"/>
      <circle cx="397" cy="211" r="5" fill="#FF7900" class="forge-spark"/>
      <circle cx="186" cy="313" r="3" fill="#FFB867" class="forge-spark forge-spark-delayed"/>
    </g>

    <g class="forge-module forge-module-web">
      <rect x="34" y="139" width="148" height="108" rx="19" class="forge-panel" filter="url(#forge-shadow)"/>
      <text x="51" y="160" class="forge-eyebrow">01 / CREATE</text>
      <g transform="translate(51 174)" fill="none" stroke-width="1.5">
        <rect width="35" height="28" rx="6" class="forge-mini-outline"/><path d="M0 8h35" class="forge-mini-outline"/>
        <path d="m12 14-4 4 4 4m11-8 4 4-4 4m-5-9-3 10" stroke="#FF7900" stroke-linecap="round" stroke-linejoin="round"/>
      </g>
      <rect x="98" y="177" width="62" height="5" rx="2.5" class="forge-mini-fill"/>
      <rect x="98" y="188" width="42" height="4" rx="2" class="forge-mini-fill"/>
      <rect x="98" y="198" width="53" height="4" rx="2" class="forge-mini-fill"/>
      <text x="51" y="229" class="forge-module-label">Web development</text>
    </g>

    <g class="forge-module forge-module-cloud">
      <rect x="225" y="30" width="150" height="100" rx="19" class="forge-panel" filter="url(#forge-shadow)"/>
      <text x="242" y="51" class="forge-eyebrow">02 / CONNECT</text>
      <g transform="translate(241 62)" fill="none" stroke-width="1.6">
        <path d="M6 24h32a8 8 0 0 0 1-16 12 12 0 0 0-22-3A10 10 0 0 0 6 24Z" class="forge-mini-outline"/>
        <path d="M24 13v15m-4-4 4 4 4-4" stroke="#FF7900" stroke-linecap="round" stroke-linejoin="round"/>
        <rect x="55" y="2" width="54" height="10" rx="4" class="forge-mini-outline"/><rect x="55" y="18" width="54" height="10" rx="4" class="forge-mini-outline"/>
        <circle cx="63" cy="7" r="1.8" fill="#FF7900" stroke="none"/><circle cx="63" cy="23" r="1.8" fill="#FF7900" stroke="none"/>
      </g>
      <text x="242" y="113" class="forge-module-label">Cloud infrastructure</text>
    </g>

    <g class="forge-module forge-module-seo">
      <rect x="423" y="147" width="143" height="108" rx="19" class="forge-panel" filter="url(#forge-shadow)"/>
      <text x="440" y="169" class="forge-eyebrow">03 / DISCOVER</text>
      <g transform="translate(440 183)">
        <rect x="4" y="22" width="9" height="11" rx="3" class="forge-mini-fill"/><rect x="20" y="14" width="9" height="19" rx="3" class="forge-mini-fill"/><rect x="36" y="3" width="9" height="30" rx="3" fill="#FF7900" fill-opacity=".65"/>
        <g class="forge-search-lens"><circle cx="79" cy="11" r="12" fill="none" class="forge-mini-outline" stroke-width="2"/><path d="m88 20 10 10" stroke="#FF7900" stroke-width="3" stroke-linecap="round"/></g>
      </g>
      <text x="440" y="238" class="forge-module-label">SEO &amp; visibility</text>
    </g>

    <g class="forge-module forge-module-diagnostic">
      <rect x="38" y="371" width="169" height="100" rx="19" class="forge-panel" filter="url(#forge-shadow)"/>
      <text x="55" y="393" class="forge-eyebrow">05 / CARE</text>
      <path d="M55 420h23l7-10 10 22 10-33 10 25 7-9h20" fill="none" class="forge-wave-track" stroke-width="1.5"/>
      <path d="M55 420h23l7-10 10 22 10-33 10 25 7-9h20" fill="none" stroke="#FF7900" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="forge-wave"/>
      <path d="M167 410v18m-9-9h18" stroke="#FF7900" stroke-width="2.5" stroke-linecap="round"/>
      <text x="55" y="453" class="forge-module-label">Diagnostic software</text>
    </g>

    <g class="forge-module forge-module-automation">
      <rect x="395" y="373" width="166" height="98" rx="19" class="forge-panel" filter="url(#forge-shadow)"/>
      <text x="412" y="395" class="forge-eyebrow">04 / SIMPLIFY</text>
      <g class="forge-gear" fill="none" stroke="#FF7900" stroke-width="2">
        <circle cx="428" cy="423" r="12" stroke-dasharray="4 4" stroke-width="5"/>
        <circle cx="428" cy="423" r="5"/>
      </g>
      <path d="M452 423h14m-4-4 4 4-4 4" stroke="#FF7900" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
      <rect x="478" y="411" width="20" height="24" rx="5" class="forge-mini-outline" fill="none" stroke-width="1.5"/><path d="m483 423 4 4 7-8" stroke="#FF7900" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
      <text x="412" y="454" class="forge-module-label">Business automation</text>
    </g>

    <g class="forge-caption"><circle cx="205" cy="515" r="3" fill="#FF7900"/><text x="219" y="519">BUILT TO WORK TOGETHER</text></g>
  </svg>
  <button type="button" class="forge-motion-toggle" data-forge-toggle aria-label="Pause hero animation" aria-pressed="false">
    <svg class="forge-pause-icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><rect x="4" y="3" width="2.5" height="10" rx="1"/><rect x="9.5" y="3" width="2.5" height="10" rx="1"/></svg>
    <svg class="forge-play-icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="m5 3 8 5-8 5Z"/></svg>
  </button>
</div>
