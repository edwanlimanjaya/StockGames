@props(['disabled' => false, 'checked' => false])

<input 
    type="radio" 
    @disabled($disabled) 
    @checked($checked)
    {{ $attributes->merge([
        'class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-full shadow-sm'
    ]) }} 
/>
