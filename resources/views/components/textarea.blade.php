@props(['disabled' => false])

<textarea
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'bg-[#43434E] rounded text-gray-200 border-none shadow-sm']) !!}
    rows="7"
>{{ $slot }}</textarea>
