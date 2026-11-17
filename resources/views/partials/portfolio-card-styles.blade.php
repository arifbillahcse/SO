@include('partials.portfolio-cover-styles')
<style>
    .wp-card { display:flex; flex-direction:column; min-width:0; color:inherit; background:var(--dark-2); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; transition:var(--transition); cursor:pointer; }
    .wp-card:hover { transform:translateY(-6px); box-shadow:var(--shadow-card); border-color:rgba(255,121,0,0.3); }
    .wp-card.hidden { display:none; }
    .wp-card:focus-visible { outline:3px solid var(--primary); outline-offset:5px; }
    .wp-img { aspect-ratio:1.6; flex-shrink:0; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; border-bottom:1px solid var(--border); }
    .wp-thumbnail { width:100%; height:100%; object-fit:cover; object-position:top; }
    .wp-thumbnail--video { object-fit:contain; object-position:center; background:#09090d; }
    .wp-kind { position:absolute; bottom:12px; left:12px; padding:6px 11px; color:#fff4e8; background:rgba(16,17,24,0.92); border:1px solid rgba(255,121,0,0.5); border-radius:100px; font-size:0.67rem; font-weight:700; }
    .wp-overlay { position:absolute; inset:0; background:rgba(0,0,0,0.65); display:flex; align-items:center; justify-content:center; opacity:0; transition:var(--transition); }
    .wp-card:hover .wp-overlay, .wp-card:focus-visible .wp-overlay { opacity:1; }
    .wp-view { padding:10px 22px; background:#ff7900; color:#141414; font-size:0.88rem; font-weight:700; border-radius:var(--radius-sm); }
    .wp-info { padding:22px; display:flex; flex-direction:column; flex:1; }
    .wp-tags { display:flex; gap:6px; flex-wrap:wrap; margin:18px 0 0; }
    .wp-category { font-size:.65rem; text-transform:uppercase; letter-spacing:.09em; font-weight:700; color:var(--primary); margin-bottom:12px; }
    .wp-tag { font-size:0.7rem; font-weight:700; color:var(--primary); background:var(--primary-glow); padding:2px 10px; border-radius:5px; letter-spacing:0; }
    .wp-title { font-size:1.15rem; font-weight:700; color:var(--text-primary); margin-bottom:8px; line-height:1.5; }
    .wp-client { font-size:0.8rem; color:var(--text-secondary); }
    .wp-description { color:var(--text-secondary); font-size:0.82rem; line-height:1.7; margin:14px 0 0; display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:2; overflow:hidden; }
    .wp-bottom { display:flex; justify-content:space-between; align-items:center; gap:16px; border-top:1px solid var(--border); margin-top:auto; padding-top:14px; color:var(--primary); font-size:0.78rem; font-weight:600; }
    .wp-bottom-wrap { margin-top:20px; flex:1; display:flex; flex-direction:column; justify-content:flex-end; }
    .wp-arrow { color:var(--primary); font-size:1.2rem; }
</style>
