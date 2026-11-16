<style>
.service-forge{--forge-surface:#18181f;--forge-surface-end:#24242e;--forge-edge:rgba(255,255,255,.13);--forge-wire:rgba(255,121,0,.25);--forge-mini:rgba(255,255,255,.1);--forge-ink:#efeff5;--forge-muted:#9999aa;--forge-glass:rgba(255,255,255,.055);--forge-side:#16161f;position:relative;width:100%;max-width:560px;isolation:isolate}
html[data-theme="light"] .service-forge{--forge-surface:#fff;--forge-surface-end:#f0f2f7;--forge-edge:#dce0e9;--forge-wire:rgba(169,71,0,.24);--forge-mini:#e4e8ef;--forge-ink:#151622;--forge-muted:#667187;--forge-glass:rgba(255,255,255,.7);--forge-side:#e9edf4}
.forge-scene{display:block;width:100%;height:auto;overflow:visible}
.forge-dot-field{color:var(--forge-muted);opacity:.17}
.forge-floor{stroke:var(--forge-edge);opacity:.65}
.forge-panel{fill:var(--forge-surface);stroke:var(--forge-edge);stroke-width:1}
.forge-face-start{stop-color:var(--forge-surface)}
.forge-face-end{stop-color:var(--forge-surface-end)}
.forge-connectors{stroke:var(--forge-wire)}
.forge-signals path{stroke-dasharray:10 165;animation:forge-flow 6s linear infinite}
.forge-outline{stroke:var(--forge-edge);stroke-width:1}
.forge-platform-side{fill:var(--forge-side);stroke:var(--forge-edge);stroke-width:1}
.forge-platform-inset{fill:var(--forge-mini);fill-opacity:.25;stroke:var(--forge-edge);stroke-width:1}
.forge-ground-shadow{fill:#2d1200;opacity:.07;filter:url(#forge-glow)}
.forge-core-signal{animation:forge-core-flow 3s linear infinite}
.forge-core-pulse{animation:forge-breathe 6s ease-in-out infinite;transform-origin:300px 329px}
.forge-core{animation:forge-core-lift 9s ease-in-out infinite}
.forge-glass-shell{fill:var(--forge-glass);stroke:var(--forge-edge);stroke-width:1}
.forge-glass-top{fill:var(--forge-surface);fill-opacity:.4}
.forge-glass-edge{stroke:var(--forge-edge)}
.forge-orbit-back{stroke:var(--forge-wire);stroke-width:1;stroke-dasharray:3 7}
.forge-orbit-fine{stroke:var(--forge-wire);stroke-width:.8;stroke-dasharray:2 8;animation:forge-orbit-trace 32s linear infinite}
.forge-eyebrow{font-family:var(--font);font-size:8.5px;letter-spacing:1.35px;font-weight:600;fill:var(--forge-muted)}
.forge-module-label{font-family:var(--font);font-size:12.2px;font-weight:650;letter-spacing:-.15px;fill:var(--forge-ink)}
.forge-mini-outline{stroke:var(--forge-muted);stroke-opacity:.7}
.forge-mini-fill{fill:var(--forge-mini)}
.forge-module{animation:forge-module-lift 8s ease-in-out infinite}
.forge-module-cloud{animation-delay:-1.6s}
.forge-module-seo{animation-delay:-3.2s}
.forge-module-diagnostic{animation-delay:-4.8s}
.forge-module-automation{animation-delay:-6.4s}
.forge-wave-track{stroke:var(--forge-mini)}
.forge-wave{stroke-dasharray:105 140;animation:forge-heartbeat 7s linear infinite}
.forge-gear{transform-box:fill-box;transform-origin:center;animation:forge-gear-turn 24s linear infinite}
.forge-search-lens{animation:forge-search-scan 8s ease-in-out infinite}
.forge-spark{animation:forge-spark-glow 5s ease-in-out infinite}
.forge-spark-delayed{animation-delay:-2.5s}
.forge-caption text{font-family:var(--font);font-size:9px;letter-spacing:1.5px;font-weight:600;fill:var(--forge-muted)}
.forge-motion-toggle{position:absolute;right:9%;bottom:1%;display:grid;place-items:center;width:30px;height:30px;border:1px solid var(--forge-edge);border-radius:50%;background:var(--forge-surface);color:var(--forge-muted);cursor:pointer;opacity:.65;transition:opacity .2s,border-color .2s}
.forge-motion-toggle:hover,.forge-motion-toggle:focus-visible{opacity:1;border-color:var(--primary)}
.forge-motion-toggle svg{width:13px;height:13px}
.forge-motion-toggle .forge-play-icon{display:none}
.service-forge[data-paused="true"] .forge-pause-icon{display:none}
.service-forge[data-paused="true"] .forge-play-icon{display:block}
.service-forge[data-paused="true"] *,.service-forge[data-in-view="false"] *{animation-play-state:paused!important}
@keyframes forge-flow{to{stroke-dashoffset:-175}}
@keyframes forge-core-flow{to{stroke-dashoffset:-36}}
@keyframes forge-breathe{0%,100%{opacity:.4;transform:scale(.85)}50%{opacity:1;transform:scale(1.15)}}
@keyframes forge-core-lift{0%,100%{transform:translateY(0)}50%{transform:translateY(-7px)}}
@keyframes forge-module-lift{0%,100%{transform:translateY(0)}50%{transform:translateY(-5px)}}
@keyframes forge-orbit-trace{to{stroke-dashoffset:-200}}
@keyframes forge-heartbeat{to{stroke-dashoffset:-245}}
@keyframes forge-gear-turn{to{transform:rotate(360deg)}}
@keyframes forge-search-scan{0%,100%{transform:translateX(0)}50%{transform:translateX(-5px)}}
@keyframes forge-spark-glow{0%,100%{opacity:.4}50%{opacity:1}}
@media(max-width:1024px){.service-forge{max-width:510px}}
@media(max-width:600px){.service-forge{width:min(100%,390px)}.forge-motion-toggle{width:32px;height:32px;right:5%;bottom:-1%}}
@media(prefers-reduced-motion:reduce){.service-forge *{animation:none!important;transition:none!important}.forge-motion-toggle{display:none}.forge-signals path{stroke-dasharray:none;opacity:.5}.forge-wave{stroke-dasharray:none}}
</style>
