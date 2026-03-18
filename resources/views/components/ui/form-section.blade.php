@props([
    'title' => null,
    'description' => null,
    'gridClass' => 'grid grid-cols-1 md:grid-cols-2 gap-4',
])

<section {{ $attributes->class(['workspace-form-section']) }}>
    @if($title || $description)
        <div class="workspace-form-section-head">
            @if($title)
                <h4 class="workspace-form-section-title">{{ $title }}</h4>
            @endif
            @if($description)
                <p class="workspace-form-section-description">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div class="{{ $gridClass }}">
        {{ $slot }}
    </div>
</section>
