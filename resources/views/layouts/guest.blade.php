<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
            <!-- Header Text thay cho Logo -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 text-center">{{ config('app.name', 'My Project Hub') }}</h1>
                <p class="text-gray-600 text-center mt-2">Hệ thống quản lý dự án</p>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white/80 backdrop-blur-sm shadow-xl border border-gray-200 overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
