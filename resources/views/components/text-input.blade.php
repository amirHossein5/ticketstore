@props(['disabled' => false])

<input
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'bg-[#43434E] rounded text-gray-200 border-none shadow-sm']) !!}
>
