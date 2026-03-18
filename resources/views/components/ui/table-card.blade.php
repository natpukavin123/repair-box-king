@props([
    'bodyClass' => '',
])

<div {{ $attributes->class(['card workspace-table-card']) }}>
    @isset($header)
        <div class="card-header workspace-table-header">
            {{ $header }}
        </div>
    @endisset

    <div class="card-body p-0 {{ $bodyClass }}">
        <div class="workspace-table-scroll">
            {{ $slot }}
        </div>
    </div>

    @isset($footer)
        <div class="card-footer workspace-table-footer">
            {{ $footer }}
        </div>
    @endisset
</div>
