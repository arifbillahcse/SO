<style>
.dc-page{overflow:hidden}
.dc-page [id]{scroll-margin-top:100px}
.dc-hero{padding:140px 0 80px}
.dc-hero .breadcrumb{margin-bottom:38px;flex-wrap:wrap}
.dc-hero-grid{display:grid;grid-template-columns:1.2fr 1fr;gap:64px;align-items:center}
.dc-status{display:inline-flex;padding:8px 12px;border:1px solid var(--border);border-radius:30px;background:var(--primary-glow);color:var(--primary);font-size:.7rem;font-weight:700}
.dc-hero h1{font-size:clamp(2.5rem,4.3vw,3.7rem);line-height:1.12;letter-spacing:-.05em;margin:24px 0}
.dc-hero h1 em{font-style:normal;color:var(--primary)}
.dc-lead{font-size:1.05rem;line-height:1.85;color:var(--text-secondary);max-width:560px}
.dc-hero-note{font-size:.8rem;line-height:1.8;color:var(--text-muted);margin-top:20px;max-width:530px}
.dc-actions{display:flex;gap:24px;align-items:center;flex-wrap:wrap;margin-top:28px}
.dc-workflow{border:1px solid var(--border);border-radius:22px;padding:28px;background:var(--dark-2);box-shadow:var(--shadow-card)}
.dc-workflow-top{display:flex;justify-content:space-between;gap:15px;border-bottom:1px solid var(--border);padding-bottom:20px;font-size:.64rem;letter-spacing:.09em;color:var(--text-muted)}
.dc-workflow-top span:last-child{color:var(--primary)}
.dc-flow-row{display:flex;align-items:center;gap:18px;margin-top:24px}
.dc-flow-number{display:grid;place-items:center;flex-shrink:0;width:42px;height:42px;border:1px solid var(--border);border-radius:12px;background:var(--primary-glow);font-size:.7rem;color:var(--primary);font-weight:700}
.dc-flow-row strong{font-size:.95rem;letter-spacing:-.02em}
.dc-flow-row p{font-size:.75rem;color:var(--text-secondary);line-height:1.7;margin-top:5px}
.dc-workflow-foot{font-size:.7rem;color:var(--text-muted);line-height:1.8;margin-top:26px;padding-top:20px;border-top:1px solid var(--border)}
.dc-section{padding:80px 0}
.dc-alt{background:var(--dark-2);border-block:1px solid var(--border)}
.dc-three-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px;margin-top:34px}
.dc-card{padding:28px;border:1px solid var(--border);border-radius:18px;background:var(--dark)}
.dc-card-number{font-size:.64rem;letter-spacing:.08em;color:var(--primary);font-weight:700}
.dc-card h3{font-size:1.08rem;line-height:1.5;letter-spacing:-.025em;margin:16px 0 12px}
.dc-card p,.dc-copy{font-size:.88rem;line-height:1.85;color:var(--text-secondary)}
.dc-split{display:grid;grid-template-columns:1fr 1fr;gap:70px;align-items:start}
.dc-foundation>div{padding:22px 0;border-bottom:1px solid var(--border)}
.dc-foundation>div:first-child{padding-top:0}
.dc-foundation h3{font-size:1rem;margin-bottom:8px}
.dc-foundation p{font-size:.86rem;line-height:1.8;color:var(--text-secondary)}
.dc-section-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:40px;margin-bottom:32px}
.dc-section-heading>p{max-width:380px;font-size:.85rem;line-height:1.85;color:var(--text-secondary);margin-bottom:20px}
.dc-phase{border:1px solid var(--border);border-radius:13px;background:var(--dark);margin-top:12px;overflow:hidden}
.dc-phase summary{display:flex;align-items:center;gap:18px;cursor:pointer;padding:20px 24px;font-size:.93rem;font-weight:600;list-style:none}
.dc-phase summary::-webkit-details-marker{display:none}
.dc-phase-number{font-size:.72rem;color:var(--primary);font-variant-numeric:tabular-nums}
.dc-expand{margin-left:auto;color:var(--primary);font-size:1.2rem}
.dc-phase[open] .dc-expand{transform:rotate(45deg)}
.dc-phase-content{display:grid;grid-template-columns:1.5fr 1fr;gap:32px;padding:6px 24px 26px 58px}
.dc-phase-content h3{font-size:.7rem;color:var(--primary);margin-bottom:10px}
.dc-phase-content p{font-size:.84rem;line-height:1.85;color:var(--text-secondary)}
.dc-checklist{list-style:none;margin-top:26px}
.dc-checklist li{padding:14px 0 14px 22px;border-bottom:1px solid var(--border);position:relative;font-size:.86rem;line-height:1.7;color:var(--text-secondary)}
.dc-checklist li::before{content:"";position:absolute;left:0;top:23px;width:6px;height:6px;border-radius:50%;background:var(--primary)}
.dc-scope ul{list-style:disc;margin:20px 0 0 18px;color:var(--text-secondary);font-size:.84rem;line-height:1.9}
.dc-cta{display:flex;align-items:center;justify-content:space-between;gap:44px;border:1px solid var(--border);border-radius:22px;padding:40px;background:linear-gradient(120deg,var(--primary-glow),var(--dark-2))}
.dc-cta p{max-width:630px;font-size:.87rem;line-height:1.85;color:var(--text-secondary)}
.dc-cta>.btn{flex-shrink:0}
.dc-page a:focus-visible,.dc-phase summary:focus-visible{outline:2px solid var(--primary);outline-offset:4px}
.dc-solution-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px}
.dc-solution-card{display:flex;flex-direction:column;align-items:flex-start;padding:32px;border:1px solid var(--border);border-radius:20px;background:var(--dark-2);color:var(--text-primary)}
.dc-solution-card:hover{border-color:var(--primary)}
.dc-solution-card h2{font-size:1.45rem;letter-spacing:-.03em;margin:20px 0 12px}
.dc-solution-card p{font-size:.9rem;line-height:1.85;color:var(--text-secondary)}
.dc-solution-card>span:last-child{color:var(--primary);font-size:.8rem;font-weight:700;margin-top:auto;padding-top:24px}
.dc-solution-icon{display:grid;place-items:center;width:44px;height:44px;border-radius:12px;background:var(--primary-glow);color:var(--primary)}
.dc-solution-icon svg{width:24px;height:24px}
@media(max-width:1024px){.dc-hero-grid{gap:32px}.dc-split{gap:36px}.dc-cta{flex-direction:column;align-items:flex-start}}
@media(max-width:768px){.dc-hero{padding:112px 0 54px}.dc-hero-grid,.dc-split{grid-template-columns:1fr}.dc-hero h1{font-size:2.7rem}.dc-workflow{max-width:540px;width:100%}.dc-section{padding:54px 0}.dc-three-grid{grid-template-columns:1fr}.dc-section-heading{display:block}.dc-phase-content{grid-template-columns:1fr;gap:20px}.dc-solution-grid{grid-template-columns:1fr}.dc-cta{padding:28px}.dc-phase summary{font-size:.85rem;padding:18px;gap:14px}.dc-phase-content{padding:4px 20px 24px}.dc-actions{gap:20px}.dc-cta>.btn{white-space:normal;text-align:center}}
@media(max-width:360px){.dc-hero h1{font-size:2.3rem}.dc-workflow{padding:22px}.dc-flow-row{gap:12px}.dc-cta{padding:24px}.dc-card,.dc-solution-card{padding:24px}}
</style>
