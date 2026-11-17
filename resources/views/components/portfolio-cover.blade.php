@props(['project', 'variant' => 'card'])
@php($showThumbnail = $project->thumbnail_path && ! str_starts_with($project->thumbnail_path, 'portfolio/covers/'))
<div {{ $attributes->class(['portfolio-media', 'portfolio-media--detail' => $variant === 'detail']) }} data-portfolio-media data-image-state="{{ $showThumbnail ? 'pending' : 'missing' }}">
  <div class="portfolio-title-cover" aria-hidden="true">
    <span class="portfolio-cover-eyebrow">{{ $project->project_type === 'technical_demonstration' ? $project->publicEvidenceLabel() : (in_array('Demo', $project->tags ?? [], true) ? 'Design demonstration' : 'Project') }}</span>
    <span class="portfolio-cover-title">{{ $project->title }}</span>
    <span class="portfolio-cover-category">{{ $project->categoryLabel() }}</span>
    <span class="portfolio-cover-arrow" aria-hidden="true">&nearr;</span>
  </div>
  @if($showThumbnail)
  <img class="{{ $variant === 'detail' ? 'portfolio-preview' : 'wp-thumbnail' }}{{ str_starts_with($project->thumbnail_path, 'portfolio/cloud-videos/') ? ($variant === 'detail' ? ' portfolio-preview--video' : ' wp-thumbnail--video') : '' }}"
    src="{{ $project->thumbnailUrl() }}" alt="{{ $project->thumbnailAltText() }}" loading="{{ $variant === 'detail' ? 'eager' : 'lazy' }}" decoding="async" width="640" height="400">
  @endif
</div>

@pushOnce('scripts', 'portfolio-image-fallback')
<script>
(() => {
  document.querySelectorAll('[data-portfolio-media] img').forEach(image => {
    const media = image.closest('[data-portfolio-media]');
    const loaded = () => { media.dataset.imageState = 'loaded'; };
    const failed = () => {
      image.hidden = true;
      media.dataset.imageState = 'failed';
      const figure = media.closest('figure');
      if (figure) figure.querySelectorAll('figcaption').forEach(caption => caption.hidden = true);
    };
    image.addEventListener('load', loaded);
    image.addEventListener('error', failed);
    if (image.complete) { image.naturalWidth > 0 ? loaded() : failed(); }
  });
})();
</script>
@endPushOnce
