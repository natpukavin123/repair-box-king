@props([
    'label' => null,
    'hint' => null,
    'required' => false,
    'type' => 'text',
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

    <input type="{{ $type }}" {{ $attributes->class(['form-input-custom']) }}>

    @if($hint)
        <p class="workspace-field-hint">{{ $hint }}</p>
    @endif
</div>
