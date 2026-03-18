@props([
    'label' => null,
    'hint' => null,
    'required' => false,
])

<div class="workspace-field">
    @if($label)
        <label class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select {{ $attributes->class(['form-select-custom']) }}>
        {{ $slot }}
    </select>

    @if($hint)
        <p class="workspace-field-hint">{{ $hint }}</p>
    @endif
</div>
