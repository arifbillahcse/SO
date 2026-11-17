@props(['project', 'delay' => 0, 'heading' => 'h2'])
        <a href="{{ route('our-works.show', $project) }}" {{ $attributes->class(['wp-card']) }} data-category="{{ $project->category }}" data-project-type="{{ $project->project_type }}" data-video="{{ $project->publicVideoUrl() ? 'true' : 'false' }}" data-demo="{{ $project->project_type !== 'technical_demonstration' && in_array('Demo', $project->tags ?? [], true) ? 'true' : 'false' }}" data-aos="fade-up" data-aos-delay="{{ $delay }}">
          <x-portfolio-cover :project="$project" />
          <div class="wp-info">
            <p class="wp-category">{{ $project->categoryLabel() }}</p>
            @if($heading === 'h3')
            <h3 class="wp-title">{{ $project->title }}</h3>
            @else
            <h2 class="wp-title">{{ $project->title }}</h2>
            @endif
            <p class="wp-client">{{ $project->project_type === 'technical_demonstration' ? 'Technical portfolio / '.$project->attribution : (in_array('Demo', $project->tags ?? [], true) ? 'Design demonstration' : $project->client) }}</p>
            <p class="wp-description">{{ $project->publicBrief() }}</p>
            @if(count($project->technologyTags()))
            <div class="wp-tags" aria-label="Technologies and focus">
              @foreach(array_slice($project->technologyTags(), 0, 4) as $tag)
              <span class="wp-tag">{{ $tag }}</span>
              @endforeach
            </div>
            @endif
            <div class="wp-bottom-wrap"><div class="wp-bottom"><span>View project</span><span class="wp-arrow" aria-hidden="true">&nearr;</span></div></div>
          </div>
        </a>
