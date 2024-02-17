@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm text-gray-200 capitalize']) }}>
    {{ $value ?? $slot }}
</label>
