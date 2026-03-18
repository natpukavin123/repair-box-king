@props([
    'label' => null,
    'hint' => null,
    'required' => false,
    'rows' => 3,
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

    <textarea rows="{{ $rows }}" {{ $attributes->class(['form-input-custom']) }}>{{ $slot }}</textarea>

    @if($hint)
        <p class="workspace-field-hint">{{ $hint }}</p>
    @endif
</div>
