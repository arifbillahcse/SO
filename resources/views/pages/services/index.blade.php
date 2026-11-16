@extends('layouts.app')
@section('title', 'Digital Services for Websites, SEO & Cloud | Softorio')
@section('description', 'Explore Softorio digital services for websites, SEO, cloud hosting, business automation and diagnostic software. Discuss the right scope for your project.')
@section('page_schema', \App\Support\SeoHead::encode($catalogueSchema))

@push('head')
<style>
.services-page{--service-border:rgba(255,121,0,.25)}
.services-hero{position:relative;overflow:hidden;padding:145px 0 70px}
.services-hero .container{position:relative;z-index:1}
.services-hero h1{max-width:960px;margin:24px 0;font-size:clamp(2.5rem,5.2vw,4.6rem);line-height:1.09;letter-spacing:-.045em}
.services-intro{max-width:720px;color:var(--text-secondary);font-size:1.08rem;line-height:1.8}
.services-actions{display:flex;gap:14px;flex-wrap:wrap;margin-top:30px}
.services-facts{display:flex;gap:24px;flex-wrap:wrap;margin-top:44px;padding-top:24px;border-top:1px solid var(--border);color:var(--text-secondary);font-size:.88rem}
.services-facts strong{color:var(--text-primary)}
.services-core{padding:52px 0 64px;background:var(--dark-2);border-block:1px solid var(--border)}
.services-core-heading{display:flex;justify-content:space-between;align-items:end;gap:20px;margin-bottom:28px}
.services-core-heading h2{font-size:clamp(1.6rem,2.5vw,2rem);letter-spacing:-.025em}
.services-core-heading p{max-width:360px;color:var(--text-secondary);line-height:1.7;font-size:.92rem}
.services-core-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px}
.services-core-card{min-width:0;padding:24px 20px;background:var(--dark-3);border:1px solid var(--border);border-radius:18px;display:flex;flex-direction:column;align-items:start}
.services-icon{width:42px;height:42px;display:grid;place-items:center;color:var(--primary);background:var(--primary-glow);border:1px solid var(--service-border);border-radius:12px}
.services-icon svg{width:23px;height:23px}
.services-core-card h3{margin:20px 0 12px;line-height:1.4;font-size:1rem}
.services-core-card p{color:var(--text-secondary);font-size:.8rem;margin-bottom:20px;line-height:1.6}
.services-core-card a{font-size:.8rem;color:var(--primary);font-weight:600;margin-top:auto}
.services-catalogue{padding-top:84px;scroll-margin-top:100px}
.services-catalogue-intro{max-width:700px}
.services-catalogue-intro p,.services-work-heading p{color:var(--text-secondary);line-height:1.8;margin-top:14px}
.services-jump{display:flex;flex-wrap:wrap;gap:10px;margin:30px 0 44px}
.services-jump a{display:inline-flex;gap:12px;align-items:center;padding:12px 16px;border:1px solid var(--border);border-radius:100px;background:var(--dark-2);color:var(--text-primary);font-size:.84rem;transition:border-color .2s}
.services-jump span{color:var(--text-secondary);font-size:.75rem}
.services-jump a:hover{border-color:var(--primary)}
.services-group{padding:42px 0 58px;border-top:1px solid var(--border);scroll-margin-top:90px}
.services-group-heading{display:flex;justify-content:space-between;align-items:start;gap:28px;margin-bottom:28px}
.services-group-title{display:flex;align-items:center;gap:16px}
.services-group-number{font-size:.8rem;font-weight:700;color:var(--primary);width:36px;height:36px;display:grid;place-items:center;background:var(--primary-glow);border-radius:50%;flex-shrink:0}
.services-group h2{font-size:clamp(1.7rem,3vw,2.25rem);line-height:1.2;letter-spacing:-.035em}
.services-group-description{margin-top:14px;max-width:700px;color:var(--text-secondary);line-height:1.75;font-size:.94rem}
.services-group-page{color:var(--primary);white-space:nowrap;font-size:.84rem;font-weight:600;padding-top:10px}
.services-card-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}
.services-detail-card{min-width:0;display:flex;flex-direction:column;padding:28px;border-radius:20px;border:1px solid var(--border);background:var(--dark-2);transition:transform .2s,border-color .2s,box-shadow .2s}
.services-detail-card:hover{transform:translateY(-3px);border-color:var(--service-border);box-shadow:var(--shadow-card)}
.services-detail-card.is-featured{border-color:var(--primary)}
.services-card-top{display:flex;align-items:center;justify-content:space-between;color:var(--primary);margin-bottom:22px}
.services-card-top>span{font-size:.75rem;font-weight:600;letter-spacing:.08em}
.services-card-top svg{width:25px;height:25px}
.services-detail-card h3{font-size:1.13rem;line-height:1.4;letter-spacing:-.02em;margin-bottom:12px}
.services-card-copy{color:var(--text-secondary);line-height:1.75;font-size:.9rem}
.services-scope{display:grid;gap:10px;margin:22px 0 24px}
.services-scope li{display:flex;align-items:baseline;gap:10px;color:var(--text-secondary);font-size:.82rem;line-height:1.6}
.services-scope li::before{content:'';flex:0 0 5px;height:5px;border-radius:50%;background:var(--primary)}
.services-availability{font-size:.7rem;line-height:1.4;color:var(--primary);background:var(--primary-glow);border-radius:6px;padding:6px 9px;align-self:start;margin-bottom:12px}
.services-card-bottom{margin-top:auto;padding-top:18px;border-top:1px solid var(--border)}
.services-card-action{display:flex;justify-content:space-between;gap:12px;color:var(--primary);font-size:.84rem;font-weight:600;line-height:1.6}
.services-related-link{display:block;margin-top:16px;font-size:.76rem;line-height:1.6;color:var(--text-secondary)}
.services-related-link>span{display:block;color:var(--text-primary);margin-top:3px}
.services-related-link:hover>span{color:var(--primary)}
.services-work{padding:70px 0;background:var(--dark-2);border-block:1px solid var(--border)}
.services-work-heading{margin-bottom:30px;max-width:680px}
.services-work-card{display:flex;flex-direction:column;border:1px solid var(--border);border-radius:18px;overflow:hidden;background:var(--dark-3)}
.services-work-card img{width:100%;aspect-ratio:16/9;object-fit:cover}
.services-work-copy{padding:24px}
.services-work-copy>span{font-size:.75rem;color:var(--primary)}
.services-work-copy h3{font-size:1.08rem;line-height:1.5;margin-top:8px}
.services-work-copy p{color:var(--text-secondary);font-size:.82rem;margin-top:12px}
.services-cta{padding:80px 0}
.services-cta-inner{padding:clamp(28px,4vw,52px);border:1px solid var(--service-border);border-radius:24px;background:linear-gradient(120deg,var(--primary-glow),var(--dark-2) 75%);display:flex;align-items:center;justify-content:space-between;gap:32px}
.services-cta p{max-width:580px;margin-top:16px;line-height:1.8;color:var(--text-secondary)}
.services-cta .btn{flex-shrink:0}
.services-page a:focus-visible{outline:2px solid var(--primary);outline-offset:5px;border-radius:4px}
@media(max-width:1100px){.services-core-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:900px){.services-card-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:768px){.services-hero{padding:125px 0 50px}.services-core-heading,.services-group-heading,.services-cta-inner{flex-direction:column;align-items:start}.services-catalogue{padding-top:60px}.services-group-page{padding-top:0}.services-core-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.services-work{padding:55px 0}}
@media(max-width:600px){.services-card-grid{grid-template-columns:1fr}.services-detail-card{padding:25px}.services-jump{gap:8px}.services-jump a{padding:10px 13px;font-size:.76rem}.services-core-card{padding:20px 16px}.services-core-card h3{font-size:.94rem}.services-hero h1{font-size:2.6rem}.services-actions .btn{width:100%;justify-content:center}}
@media(max-width:360px){.services-core-grid{grid-template-columns:1fr}}
@media(prefers-reduced-motion:reduce){.services-detail-card,.services-jump a{transition:none}.services-detail-card:hover{transform:none}.services-page .ph-orb{animation:none}}
</style>
@endpush

@section('content')
<main class="services-page">
  <section class="services-hero">
    <div class="page-hero-bg" aria-hidden="true"><div class="ph-orb ph-orb-1"></div><div class="ph-orb ph-orb-2"></div><div class="ph-grid"></div></div>
    <div class="container">
      <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span class="bc-current">Services</span></div>
      <span class="hero-badge"><span class="badge-dot"></span> Softorio services</span>
      <h1>Build your website.<br>Connect your tools.<br><span class="text-gradient">Manage your infrastructure.</span></h1>
      <p class="services-intro">Explore website development, SEO, managed cloud, business automation and diagnostic software services. Find the support that fits your next project.</p>
      <div class="services-actions"><a class="btn btn-primary btn-lg" href="#service-catalogue">Explore Our Services <span aria-hidden="true">&darr;</span></a><a class="btn btn-outline btn-lg" href="{{ route('home') }}#contact">Discuss Your Project</a></div>
      @if($serviceGroups->isNotEmpty())<div class="services-facts"><span><strong>{{ $serviceGroups->count() }} core services</strong></span><span><strong>{{ $serviceCount }} ways to help</strong></span><span>From your first idea to ongoing support.</span></div>@endif
    </div>
  </section>
  <section class="services-core" id="all-services" aria-labelledby="services-core-title">
    <div class="container">
      <div class="services-core-heading"><h2 id="services-core-title">One team. Connected services.</h2><p>Choose a core service, or explore the detailed catalogue below.</p></div>
      <div class="services-core-grid">
        @foreach($serviceGroups as $group)
          <article class="services-core-card">
            <div class="services-icon"><x-service-icon :group="$group['key']" :icon="$group['icon']" /></div>
            <h3>{{ $group['title'] }}</h3>
            <p>{{ $group['description'] }}</p>
            @if($group['url'])<a href="{{ $group['url'] }}" aria-label="Explore {{ $group['title'] }}">Explore service <span aria-hidden="true">&nearr;</span></a>@endif
          </article>
        @endforeach
      </div>
    </div>
  </section>
  <section class="services-catalogue" id="service-catalogue" aria-labelledby="services-catalogue-title">
    <div class="container">
      <div class="services-catalogue-intro"><span class="section-tag">Find your next step</span><h2 class="section-title" id="services-catalogue-title">The details behind <span class="text-gradient">what we do.</span></h2><p>Start with the service you need today. We can help connect it with the rest of your website, tools and infrastructure.</p></div>
      <nav class="services-jump" aria-label="Service categories">
        @foreach($serviceGroups as $group)
          @if(count($group['items']))
          <a href="#{{ $group['key'] }}">{{ $group['title'] }} <span>{{ count($group['items']) }}</span></a>
          @endif
        @endforeach
      </nav>
      @foreach($serviceGroups as $group)
        @if(count($group['items']))
        <section class="services-group" id="{{ $group['key'] }}" aria-labelledby="{{ $group['key'] }}-title">
          <div class="services-group-heading">
            <div><div class="services-group-title"><span class="services-group-number" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h2 id="{{ $group['key'] }}-title">{{ $group['title'] }}</h2></div><p class="services-group-description">{{ $group['description'] }}</p></div>
            @if($group['url'])<a class="services-group-page" href="{{ $group['url'] }}">Explore {{ $group['title'] }} <span aria-hidden="true">&nearr;</span></a>@endif
          </div>
          <div class="services-card-grid">
            @foreach($group['items'] as $item)
              <article class="services-detail-card{{ $item['featured'] ? ' is-featured' : '' }}" id="service-{{ $item['key'] }}">
                <div class="services-card-top"><span aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><x-service-icon :group="$group['key']" :icon="$item['icon']" /></div>
                @if($item['featured'])<span class="services-availability">Featured service</span>@endif
                @isset($item['availability'])<span class="services-availability">{{ $item['availability'] }}</span>@endisset
                <h3>{{ $item['title'] }}</h3>
                <p class="services-card-copy">{{ $item['description'] }}</p>
                <ul class="services-scope">@foreach($item['scope'] as $scope)<li>{{ $scope }}</li>@endforeach</ul>
                <div class="services-card-bottom">
                  @if($item['primary_action']['url'])<a class="services-card-action" href="{{ $item['primary_action']['url'] }}" aria-label="{{ $item['primary_action']['label'] }}: {{ $item['title'] }}">{{ $item['primary_action']['label'] }} <span aria-hidden="true">&nearr;</span></a>@endif
                  @if($item['related_project'])
                    <a class="services-related-link" href="{{ route('our-works.show', $item['related_project']) }}">{{ $item['related_project']->project_type === 'technical_demonstration' ? 'Related technical work' : 'Related website project' }}<span>{{ $item['related_project']->title }} <span aria-hidden="true">&rarr;</span></span></a>
                  @endif
                </div>
              </article>
            @endforeach
          </div>
        </section>
        @endif
      @endforeach
      @if($serviceCount === 0)<p class="services-group-description" style="padding-bottom:48px">Tell us what you're planning. Contact our team to discuss the services available for your project.</p>@endif
    </div>
  </section>
  @if($selectedProjects->isNotEmpty())
    <section class="services-work" aria-labelledby="services-work-title"><div class="container">
      <div class="services-work-heading"><span class="section-tag">Explore related work</span><h2 class="section-title" id="services-work-title">A closer look at <span class="text-gradient">the technical work.</span></h2><p>Explore these topics from Md Arif Billah's technical portfolio, including the available references and walkthroughs.</p></div>
      <div class="services-card-grid">
        @foreach($selectedProjects as $project)
          <a class="services-work-card" href="{{ route('our-works.show', $project) }}">
            @if($project->thumbnail_path)<img src="{{ asset('storage/'.$project->thumbnail_path) }}" alt="{{ $project->thumbnailAltText() }}" loading="lazy" width="640" height="360">@endif
            <div class="services-work-copy"><span>{{ $project->publicEvidenceLabel() }}</span><h3>{{ $project->title }}</h3><p>Explore the project <span aria-hidden="true">&nearr;</span></p></div>
          </a>
        @endforeach
      </div>
    </div></section>
  @endif
  <section class="services-cta"><div class="container"><div class="services-cta-inner"><div><span class="section-tag">Let's work together</span><h2 class="section-title">What are you <span class="text-gradient">planning next?</span></h2><p>Tell us about your website, workflow or infrastructure. We'll discuss the requirements and help you choose a practical next step.</p></div><a class="btn btn-primary btn-lg" href="{{ route('home') }}#contact">Discuss Your Project <span aria-hidden="true">&nearr;</span></a></div></div></section>
</main>
@endsection
