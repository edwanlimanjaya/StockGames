@props(['striped' => false, 'hover' => true])

<table {{ $attributes->merge([
    'class' => 'w-full text-sm text-left text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm ' .
               ($striped ? 'divide-y divide-gray-200 dark:divide-gray-700' : '') .
               ($hover ? ' hover:table-row-hover' : '')
]) }}>
    {{ $slot }}
</table>
