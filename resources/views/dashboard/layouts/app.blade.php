<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @if(isset($title))
        <title>{{ $title }}</title>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class='bg-[#26262D]'>
    @include('dashboard.layouts.navigation')

    {{ $secondNavigation ?? ''}}

    <main class="container mx-auto px-8 py-10 text-gray-200 min-h-screen">
        {{ $slot }}
    </main>
</body>
</html>
