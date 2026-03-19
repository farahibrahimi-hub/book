<div {{ $attributes->merge(['class' => 'p-4 border rounded-lg']) }}>
    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $title }}</div>
    <div class="text-2xl font-bold">{{ $value }}</div>
    @if(isset($label))<div class="text-xs text-gray-400">{{ $label }}</div>@endif
</div>