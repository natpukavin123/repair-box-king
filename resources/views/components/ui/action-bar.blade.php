@props([
    'title' => null,
    'description' => null,
])

<div {{ $attributes->class(['workspace-toolbar']) }}>
    @if($title || $description)
        <div class="workspace-toolbar-main">
            @if($title)
                <h3 class="workspace-toolbar-title">{{ $title }}</h3>
            @endif
            @if($description)
                <p class="workspace-toolbar-description">{{ $description }}</p>
            @endif
        </div>
    @endif

    @if(trim((string) $slot) !== '')
        <div class="workspace-toolbar-actions">
            {{ $slot }}
        </div>
    @endif
</div>
