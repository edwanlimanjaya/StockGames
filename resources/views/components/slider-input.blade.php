@props([
    'disabled' => false,
    'min' => 1,
    'max' => 4,
    'step' => 1,
    'value' => 2,
])

<input
    type="range"
    min="{{ $min }}"
    max="{{ $max }}"
    step="{{ $step }}"
    value="{{ $value }}"
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => ''
    ]) }}
>
<style>
   
</style>
