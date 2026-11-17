<style>
.about-page{--ab-line:var(--border);overflow:hidden}
.ab-wrap{max-width:1200px;margin:auto;padding:0 24px}
.ab-eyebrow{display:inline-flex;align-items:center;gap:10px;color:var(--primary);font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;font-weight:700}
.ab-eyebrow::before{content:"";width:22px;height:2px;background:currentColor}
.ab-hero{position:relative;padding:122px 0 76px}
.ab-hero::before{content:"";position:absolute;inset:0;background:radial-gradient(ellipse at 85% 40%,var(--primary-glow),transparent 55%);pointer-events:none}
.ab-hero-grid{position:relative;display:grid;grid-template-columns:1fr 1fr;align-items:center;gap:64px}
.ab-breadcrumb{display:flex;gap:9px;align-items:center;font-size:.75rem;color:var(--text-muted);margin-bottom:40px}
.ab-breadcrumb a{color:var(--text-secondary)}
.ab-hero h1{font-size:clamp(2.7rem,4.6vw,4.25rem);font-weight:750;letter-spacing:-.055em;line-height:1.08;margin:24px 0;color:var(--text-primary)}
.ab-hero h1 em{font-style:normal;color:var(--primary)}
.ab-lead{max-width:445px;color:var(--text-secondary);font-size:1.02rem;line-height:1.85}
.ab-actions{display:flex;align-items:center;gap:24px;flex-wrap:wrap;margin-top:30px}
.ab-text-link{display:inline-flex;align-items:center;gap:9px;color:var(--text-primary);font-weight:600;font-size:.85rem}
.ab-text-link svg,.ab-actions .btn svg{width:17px;height:17px;flex-shrink:0}
.ab-text-link:hover{color:var(--primary)}
.ab-signoff{margin-top:34px;padding-top:20px;border-top:1px solid var(--border);font-size:.75rem;color:var(--text-muted);letter-spacing:.015em}
.ab-visual{min-width:0;position:relative}
.ab-visual>svg{width:100%;height:auto;display:block;overflow:visible}
.ab-art-panel{fill:var(--dark-3);stroke:var(--border)}
.ab-art-edge{stroke:var(--border)}
.ab-art-text{fill:var(--text-primary);font-family:var(--font);font-weight:600}
.ab-art-muted{fill:var(--text-muted);font-family:var(--font)}
.ab-art-raise{animation:ab-settle 1.2s ease-out both}
.ab-art-raise-delayed{animation:ab-settle 1.2s .2s ease-out both}
.ab-art-signal{stroke-dasharray:6 9;animation:ab-signal 2.8s ease-out both}
@keyframes ab-settle{from{transform:translateY(15px);opacity:.4}to{transform:translateY(0);opacity:1}}
@keyframes ab-signal{from{stroke-dashoffset:90;opacity:.2}to{stroke-dashoffset:0;opacity:1}}
.ab-section{padding:80px 0}
.ab-story{border-top:1px solid var(--border)}
.ab-story-grid{display:grid;grid-template-columns:1.05fr 1fr;gap:84px;align-items:center}
.ab-title{font-size:clamp(1.9rem,3vw,2.7rem);font-weight:700;line-height:1.2;letter-spacing:-.04em;color:var(--text-primary);margin:20px 0}
.ab-copy{font-size:.94rem;line-height:1.9;color:var(--text-secondary);max-width:520px}
.ab-copy+p{margin-top:14px}
.ab-founder{position:relative;display:flex;align-items:flex-end;gap:22px;min-height:300px;padding:26px;border:1px solid var(--border);border-radius:24px;background:linear-gradient(140deg,var(--primary-glow),var(--dark-3) 70%);overflow:hidden}
.ab-founder::before{content:"";position:absolute;width:250px;height:250px;border:1px solid rgba(255,121,0,.2);border-radius:50%;left:-38px;bottom:-68px}
.ab-founder-photo{position:relative;flex:0 0 48%;align-self:stretch;display:flex;align-items:flex-end;min-width:0}
.ab-founder-photo img{width:100%;height:auto;max-height:290px;object-fit:contain;object-position:bottom;display:block;position:relative;z-index:1}
.ab-founder-info{position:relative;padding:20px 0;min-width:0}
.ab-founder-label{font-size:.68rem;letter-spacing:.1em;text-transform:uppercase;color:var(--primary);font-weight:700}
.ab-founder h3{font-size:1.35rem;line-height:1.35;letter-spacing:-.03em;margin:12px 0 6px}
.ab-founder-role{font-size:.77rem;color:var(--text-secondary);line-height:1.7}
.ab-founder-note{font-size:.74rem;color:var(--text-muted);line-height:1.7;padding-top:16px;margin-top:16px;border-top:1px solid var(--border)}
.ab-services{background:var(--dark-2);border-block:1px solid var(--border)}
.ab-section-head{display:flex;justify-content:space-between;align-items:flex-end;gap:40px;margin-bottom:34px}
.ab-section-head .ab-title{margin-bottom:0}
.ab-services-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:16px}
.ab-service{grid-column:span 2;display:flex;flex-direction:column;align-items:flex-start;padding:26px;background:var(--dark-3);border:1px solid var(--border);border-radius:18px;transition:transform .2s,border-color .2s;min-width:0}
.ab-service:nth-child(n+4){grid-column:span 3}
.ab-service:hover{transform:translateY(-3px);border-color:rgba(255,121,0,.55)}
.ab-service-icon{display:flex;align-items:center;justify-content:center;width:42px;height:42px;border-radius:12px;color:var(--primary);background:var(--primary-glow);margin-bottom:20px}
.ab-service-icon svg{width:24px;height:24px}
.ab-service h3{font-size:1.05rem;letter-spacing:-.02em;color:var(--text-primary);line-height:1.5;margin-bottom:9px}
.ab-service p{font-size:.83rem;color:var(--text-secondary);line-height:1.8;max-width:360px}
.ab-service-bottom{display:flex;align-items:center;justify-content:space-between;width:100%;gap:12px;margin-top:auto;padding-top:18px;font-size:.73rem;font-weight:600;color:var(--primary)}
.ab-service-bottom>svg{width:17px;height:17px}
.ab-development{display:inline-block;font-size:.65rem;font-weight:600;color:var(--text-secondary);border:1px solid var(--border);padding:4px 9px;border-radius:30px}
.ab-values-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:38px;margin-top:38px}
.ab-value{border-top:1px solid var(--border);padding-top:24px}
.ab-value-number{font-size:.72rem;color:var(--primary);font-variant-numeric:tabular-nums;font-weight:700;letter-spacing:.08em}
.ab-value h3{font-size:1.15rem;color:var(--text-primary);letter-spacing:-.025em;margin:18px 0 12px}
.ab-value p{font-size:.88rem;line-height:1.85;color:var(--text-secondary)}
.ab-process{padding-top:0}
.ab-process-panel{background:var(--dark-2);border:1px solid var(--border);border-radius:24px;padding:42px}
.ab-process-heading{display:flex;justify-content:space-between;align-items:flex-end;gap:36px;margin-bottom:42px}
.ab-process-heading .ab-title{margin-bottom:0}
.ab-process-heading>p{max-width:290px;font-size:.87rem;line-height:1.8;color:var(--text-secondary)}
.ab-steps{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:26px;position:relative}
.ab-steps::before{content:"";position:absolute;top:21px;left:0;right:0;height:1px;background:var(--border)}
.ab-step{position:relative}
.ab-step-number{display:grid;place-items:center;width:42px;height:42px;border:1px solid var(--border);border-radius:50%;background:var(--dark-3);color:var(--primary);font-size:.75rem;font-weight:700}
.ab-step h3{font-size:.96rem;margin:20px 0 10px;letter-spacing:-.02em}
.ab-step p{font-size:.8rem;color:var(--text-secondary);line-height:1.8}
.ab-cta{padding:0 0 80px}
.ab-cta-panel{position:relative;overflow:hidden;display:flex;justify-content:space-between;align-items:center;gap:48px;border-radius:24px;border:1px solid rgba(255,121,0,.25);padding:48px 52px;background:linear-gradient(105deg,var(--primary-glow),var(--dark-2) 80%)}
.ab-cta-panel::after{content:"";position:absolute;right:-50px;top:-120px;width:330px;height:330px;border:1px solid rgba(255,121,0,.12);border-radius:50%;pointer-events:none}
.ab-cta .ab-title{margin-top:0;max-width:600px}
.ab-cta p{font-size:.91rem;line-height:1.8;color:var(--text-secondary);max-width:510px}
.ab-cta .btn{position:relative;flex-shrink:0;z-index:1}
.about-page a:focus-visible{outline:2px solid var(--primary);outline-offset:5px}
@media(max-width:1024px){.ab-hero-grid{gap:30px}.ab-story-grid{gap:40px}.ab-founder{padding:20px;gap:16px}.ab-founder h3{font-size:1.16rem}.ab-process-panel{padding:32px}.ab-cta-panel{padding:38px}}
@media(max-width:768px){
.ab-hero{padding:106px 0 48px}.ab-hero-grid{grid-template-columns:1fr;gap:38px}.ab-breadcrumb{margin-bottom:28px}.ab-hero h1{font-size:clamp(2.8rem,8vw,3.8rem);max-width:560px}.ab-lead{max-width:520px}.ab-visual{max-width:520px;width:100%;margin:auto}.ab-signoff{max-width:520px}
.ab-section{padding:54px 0}.ab-story-grid{grid-template-columns:1fr;gap:30px}.ab-founder{max-width:520px;width:100%;min-height:270px}.ab-founder-photo img{max-height:250px}.ab-founder h3{font-size:1.3rem}.ab-section-head{align-items:flex-start;gap:20px;flex-direction:column}
.ab-service{grid-column:span 3!important;padding:22px}.ab-service:last-child{grid-column:span 6!important}.ab-values-grid{gap:24px}.ab-value h3{font-size:1rem}.ab-values-grid p{font-size:.82rem}
.ab-process{padding-top:0}.ab-process-heading{align-items:flex-start;flex-direction:column;gap:18px}.ab-steps{grid-template-columns:repeat(2,minmax(0,1fr));row-gap:30px}.ab-steps::before{display:none}.ab-process-heading{margin-bottom:30px}
.ab-cta{padding-bottom:54px}.ab-cta-panel{flex-direction:column;align-items:flex-start;gap:26px}.ab-cta .ab-title{font-size:2rem}
}
@media(max-width:480px){.ab-wrap{padding:0 20px}.ab-title{font-size:1.85rem}.ab-actions{gap:20px}.ab-hero h1{font-size:2.75rem}.ab-lead{font-size:.94rem}.ab-service{grid-column:span 6!important}.ab-values-grid{grid-template-columns:1fr;gap:26px}.ab-value h3{font-size:1.1rem;margin-top:12px}.ab-value p{font-size:.89rem}.ab-process-panel{padding:26px 22px;border-radius:18px}.ab-steps{gap:25px 18px}.ab-founder{gap:12px;padding:20px 16px;min-height:250px}.ab-founder h3{font-size:1.12rem}.ab-founder-role{font-size:.72rem}.ab-founder-note{font-size:.7rem}.ab-cta-panel{padding:30px 24px;border-radius:18px}}
@media(prefers-reduced-motion:reduce){.about-page *{animation:none!important;scroll-behavior:auto;transition:none!important}.ab-service:hover{transform:none}}
</style>
