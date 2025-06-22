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
        
        <!-- Custom CSS for text overflow and line clamp -->
        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .line-clamp-3 {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .break-words {
                word-wrap: break-word;
                word-break: break-word;
                overflow-wrap: break-word;
            }
            /* Modal z-index fix */
            .z-50 {
                z-index: 50;
            }
            /* Smooth transitions for hover effects */
            .transition-colors {
                transition: color 0.2s ease-in-out;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        
        <!-- Prevent back button access after logout -->
        <script>
            // Disable browser back button functionality for authenticated pages
            if (window.history && window.history.pushState) {
                window.history.pushState('forward', null, '');
                window.addEventListener('popstate', function() {
                    window.history.pushState('forward', null, '');
                });
            }
            
            // Clear browser cache on page unload
            window.addEventListener('beforeunload', function() {
                if (window.performance && window.performance.navigation.type === 1) {
                    // Page was refreshed
                    return;
                }
                // Clear any cached data
                if ('caches' in window) {
                    caches.keys().then(function(names) {
                        names.forEach(function(name) {
                            caches.delete(name);
                        });
                    });
                }
            });
        </script>
    </body>
</html>
