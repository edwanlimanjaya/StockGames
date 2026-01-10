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
        'class' => 'w-full h-2 bg-gray-300 rounded-lg appearance-none cursor-pointer focus:outline-none focus:ring-0'
    ]) }}
>
<style>
    input[type="range"]::-webkit-slider-thumb {
        background-color: #6b7280;
        border: none;
        width: 1rem;         /* sekitar 16px, tapi scalable */
        height: 1rem;
        border-radius: 50%;
        appearance: none;
        margin-top: -0.4375rem; /* sekitar -7px, tapi adaptif */
    }

    input[type="range"]::-webkit-slider-runnable-track {
        background-color: #d1d5db;
        height: 0.125rem;     /* sekitar 2px */
        border-radius: 0.25rem;
    }

    input[type="range"]:focus::-webkit-slider-runnable-track {
        background-color: #d1d5db;
    }

    input[type="range"]::-moz-range-thumb {
        background-color: #6b7280;
        border: none;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
    }

    input[type="range"]::-moz-range-track {
        background-color: #d1d5db;
        height: 0.125rem;
        border-radius: 0.25rem;
    }
</style>
