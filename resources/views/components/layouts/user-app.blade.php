<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="box-border bg-zinc-100">
    <div class="box-border">
        @livewire('user.sidebar')
    </div>
    <div class="float-right w-[100%] lg:w-[calc(100%-256px)]">
        {{ $slot }}
    </div>
        
    @livewireScripts
</body>

</html>