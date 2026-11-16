@if($whatsappChatUrl)
<style>
.whatsapp-chat{position:fixed;right:max(24px,env(safe-area-inset-right));bottom:calc(24px + env(safe-area-inset-bottom));z-index:900;display:flex;align-items:center;justify-content:center;width:56px;height:56px;border:1px solid rgba(255,255,255,.25);border-radius:50%;background:#ff7900;color:#17110a;box-shadow:0 6px 24px rgba(255,121,0,.28);text-decoration:none;transition:transform .2s,box-shadow .2s}
.whatsapp-chat>svg{display:block;width:29px;height:29px;flex-shrink:0}
.whatsapp-chat:hover{transform:translateY(-3px);box-shadow:0 9px 28px rgba(255,121,0,.4)}
.whatsapp-chat:focus-visible{outline:3px solid var(--text-primary);outline-offset:4px}
.whatsapp-chat-label{position:absolute;right:calc(100% + 12px);padding:9px 13px;border:1px solid var(--border);border-radius:10px;background:var(--dark-3);color:var(--text-primary);font-size:.78rem;font-weight:600;line-height:1.5;white-space:nowrap;opacity:0;visibility:hidden;pointer-events:none;transition:opacity .2s}
.whatsapp-chat:hover .whatsapp-chat-label,.whatsapp-chat:focus-visible .whatsapp-chat-label{opacity:1;visibility:visible}
@media(max-width:600px){.whatsapp-chat{width:52px;height:52px;right:max(16px,env(safe-area-inset-right));bottom:calc(16px + env(safe-area-inset-bottom))}.whatsapp-chat-label{display:none}}
@media(prefers-reduced-motion:reduce){.whatsapp-chat,.whatsapp-chat-label{transition:none}.whatsapp-chat:hover{transform:none}}
</style>
<a class="whatsapp-chat" href="{{ $whatsappChatUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Chat with Softorio on WhatsApp (opens in a new tab)">
  <svg width="29" height="29" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 11.6a9 9 0 0 1-13.4 7.9L3 21l1.5-4.6A9 9 0 1 1 21 11.6Z"/>
    <path d="m8.8 7.5 1.3 2.6-1.2 1.2a9.5 9.5 0 0 0 3.8 3.8l1.2-1.2 2.6 1.3c0 1.3-1 2-2.2 1.8-4.3-.7-7.4-3.8-8.1-8.1-.2-1.2.5-2.2 1.8-2.2Z"/>
  </svg>
  <span class="whatsapp-chat-label" aria-hidden="true">Chat on WhatsApp</span>
</a>
@endif
