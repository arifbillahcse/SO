<style>
.case-library,.case-detail{--case-accent-border:rgba(255,121,0,.23)}
.case-hero{position:relative;overflow:hidden;padding:140px 0 64px}
.case-hero .container{position:relative;z-index:1}
.case-hero .breadcrumb{margin-bottom:30px;flex-wrap:wrap;row-gap:8px}
.case-hero h1{font-size:clamp(2.4rem,5vw,4.15rem);line-height:1.1;letter-spacing:-.045em;margin:22px 0;max-width:950px}
.case-lead{max-width:750px;color:var(--text-secondary);line-height:1.85;font-size:1.04rem}
.case-hero-bottom{display:flex;flex-wrap:wrap;align-items:center;gap:22px;margin-top:36px;padding-top:24px;border-top:1px solid var(--border);color:var(--text-secondary);font-size:.82rem}
.case-hero-bottom>span:first-child{color:var(--text-primary);font-weight:600}
.case-hero-bottom a{color:var(--primary);font-weight:600;margin-left:auto}
.case-collection{padding:60px 0 80px;background:var(--dark-2);border-top:1px solid var(--border);scroll-margin-top:90px}
.case-eyebrow{font-size:.73rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--primary)}
.case-featured{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(0,1fr);gap:48px;padding:42px;background:var(--dark-3);border:1px solid var(--case-accent-border);border-radius:24px;margin-bottom:76px}
.case-tags{display:flex;gap:7px;flex-wrap:wrap;margin:18px 0}
.case-tags>span{font-size:.7rem;color:var(--text-secondary);border:1px solid var(--border);padding:5px 10px;border-radius:6px;line-height:1.4}
.case-featured h2{font-size:clamp(1.65rem,2.7vw,2.25rem);line-height:1.25;letter-spacing:-.035em;margin-bottom:18px}
.case-featured-copy>p{font-size:.92rem;color:var(--text-secondary);line-height:1.85;margin-bottom:24px}
.case-featured-summary{padding:28px;border-radius:18px;background:linear-gradient(145deg,var(--primary-glow),var(--dark-2));border:1px solid var(--case-accent-border);align-self:stretch}
.case-summary-note{font-size:.78rem;line-height:1.7;color:var(--text-secondary);margin:12px 0 22px}
.case-featured-metric{display:grid;grid-template-columns:minmax(100px,.8fr) minmax(0,1fr);gap:20px;align-items:center;padding:18px 0;border-top:1px solid var(--border)}
.case-featured-metric strong{color:var(--primary);font-size:clamp(1.2rem,2vw,1.65rem);letter-spacing:-.035em;line-height:1.3}
.case-featured-metric span{color:var(--text-secondary);font-size:.78rem;line-height:1.6}
.case-collection-heading{display:flex;justify-content:space-between;align-items:end;gap:36px;margin-bottom:30px}
.case-collection-heading h2,.case-contact h2{font-size:clamp(1.75rem,3vw,2.5rem);line-height:1.2;letter-spacing:-.035em;margin-top:12px}
.case-collection-heading>p{max-width:310px;color:var(--text-secondary);font-size:.86rem;line-height:1.75}
.case-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px}
.case-card{display:flex;flex-direction:column;min-width:0;padding:28px;border:1px solid var(--border);border-radius:20px;background:var(--dark-3);color:inherit;transition:transform .2s,border-color .2s,box-shadow .2s}
.case-card:hover{transform:translateY(-4px);border-color:var(--case-accent-border);box-shadow:var(--shadow-card)}
.case-card-top{display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:.72rem;color:var(--text-secondary)}
.case-card-top>span:first-child{color:var(--primary);font-weight:600;font-variant-numeric:tabular-nums}
.case-type{padding:5px 9px;border-radius:100px;background:var(--primary-glow);color:var(--primary);font-size:.65rem;font-weight:600}
.case-card h3{font-size:1.12rem;line-height:1.45;letter-spacing:-.02em;margin:0 0 14px}
.case-card-description{color:var(--text-secondary);font-size:.86rem;line-height:1.8;margin-bottom:26px}
.case-card-bottom{margin-top:auto}
.case-card-note{color:var(--text-muted);font-size:.72rem;line-height:1.65;margin-bottom:18px}
.case-card-metrics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;padding:18px 0;border-block:1px solid var(--border)}
.case-card-metrics strong{display:block;font-size:1rem;color:var(--primary);line-height:1.5;font-variant-numeric:tabular-nums;letter-spacing:-.02em}
.case-card-metrics span{display:block;color:var(--text-secondary);font-size:.66rem;line-height:1.6;margin-top:6px}
.case-read{display:flex;justify-content:space-between;gap:15px;margin-top:20px;font-size:.8rem;font-weight:600;color:var(--primary)}
.case-empty{color:var(--text-secondary);line-height:1.8;padding:20px 0}
.case-contact{padding:76px 0}
.case-contact-inner{display:flex;justify-content:space-between;align-items:center;gap:40px;padding:42px;border:1px solid var(--case-accent-border);border-radius:24px;background:linear-gradient(120deg,var(--primary-glow),var(--dark-2) 75%)}
.case-contact-inner p{max-width:590px;color:var(--text-secondary);line-height:1.8;margin-top:18px;font-size:.95rem}
.case-contact-actions{display:flex;flex-direction:column;align-items:center;gap:18px;flex-shrink:0}
.case-text-link{color:var(--primary);font-size:.84rem;font-weight:600}
.case-library a:focus-visible,.case-detail a:focus-visible{outline:2px solid var(--primary);outline-offset:5px}
.case-detail .case-hero{padding-bottom:48px}
.case-detail .case-hero h1{max-width:920px;font-size:clamp(2.1rem,4.5vw,3.5rem);margin-top:20px}
.case-detail .case-hero .case-tags{margin:0 0 18px}
.case-detail-content{padding:54px 0 76px;border-top:1px solid var(--border)}
.case-reading{max-width:760px;margin:0 auto}
.case-disclosure{display:flex;gap:16px;padding:20px 22px;border:1px solid var(--case-accent-border);border-radius:14px;background:var(--primary-glow);color:var(--text-secondary);font-size:.86rem;line-height:1.8;margin-bottom:34px}
.case-disclosure strong{color:var(--text-primary)}
.case-detail-metrics{margin-bottom:42px}
.case-detail-metrics>p{color:var(--text-secondary);font-size:.8rem;line-height:1.8;margin-bottom:16px}
.case-stat-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
.case-stat{padding:22px 18px;border:1px solid var(--border);border-radius:14px;background:var(--dark-2);min-width:0}
.case-stat strong{display:block;color:var(--primary);font-size:clamp(1.15rem,2.1vw,1.65rem);line-height:1.4;letter-spacing:-.035em;font-variant-numeric:tabular-nums}
.case-stat span{display:block;color:var(--text-secondary);font-size:.76rem;line-height:1.65;margin-top:10px}
.case-detail .article-body{font-size:1rem;line-height:1.9;color:var(--text-secondary)}
.case-detail .article-body h2{font-size:clamp(1.3rem,2.4vw,1.55rem);line-height:1.4;font-weight:700;letter-spacing:-.025em;color:var(--text-primary);margin:38px 0 18px;padding-top:26px;border-top:1px solid var(--border)}
.case-detail .article-body h2:first-child{margin-top:0}
.case-detail .article-body h3{font-size:1.15rem;line-height:1.45;color:var(--text-primary);margin:26px 0 14px}
.case-detail .article-body p{margin-bottom:20px}
.case-detail .article-body ul,.case-detail .article-body ol{padding-left:22px;margin:0 0 24px}
.case-detail .article-body li{margin-bottom:12px}
.case-detail .article-body a{color:var(--primary);text-decoration:underline;text-underline-offset:4px}
.case-related{padding:62px 0;background:var(--dark-2);border-block:1px solid var(--border)}
.case-related .case-card h3{margin-bottom:22px}
.case-related .case-read{margin-top:auto;padding-top:20px;border-top:1px solid var(--border)}
.case-card,.case-featured,.case-stat,.case-reading{overflow-wrap:anywhere}
@media(max-width:1024px){.case-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.case-featured{gap:28px;padding:32px}.case-featured-metric{grid-template-columns:1fr;gap:7px}}
@media(max-width:768px){.case-hero{padding-top:118px}.case-featured{grid-template-columns:1fr;margin-bottom:52px}.case-featured-metric{grid-template-columns:minmax(100px,.8fr) minmax(0,1fr)}.case-collection-heading,.case-contact-inner{flex-direction:column;align-items:start;gap:20px}.case-contact-actions{align-items:start}.case-hero-bottom{gap:14px}.case-hero-bottom a{margin-left:0}.case-detail-content{padding-top:32px}.case-related .case-grid{grid-template-columns:1fr}}
@media(max-width:600px){.case-hero{padding-bottom:42px}.case-hero h1{font-size:2.5rem}.case-collection{padding-top:32px}.case-grid{grid-template-columns:1fr}.case-card{padding:24px}.case-featured{padding:24px;border-radius:18px}.case-featured-summary{padding:22px}.case-contact{padding:48px 0}.case-contact-inner{padding:26px}.case-lead{font-size:.95rem}.case-detail .case-hero h1{font-size:2.1rem}.case-stat-grid{grid-template-columns:1fr}.case-stat{display:flex;align-items:center;justify-content:space-between;gap:22px;padding:18px}.case-stat strong{font-size:1.3rem;max-width:55%}.case-stat span{margin-top:0;max-width:45%;font-size:.74rem}.case-disclosure{padding:18px;font-size:.82rem}.case-detail .article-body{font-size:.95rem}.case-hero-bottom>span:nth-child(2){flex-basis:100%}}
@media(prefers-reduced-motion:reduce){.case-card{transition:none}.case-card:hover{transform:none}.case-library .ph-orb,.case-detail .ph-orb{animation:none}}
</style>
