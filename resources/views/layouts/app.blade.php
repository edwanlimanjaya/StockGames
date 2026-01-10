<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @push('styles')
    <style>
        input[type="range"]::-webkit-slider-thumb {
            background-color: #6b7280;
            border: none;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            appearance: none;
            margin-top: -0.4375rem;
        }

        input[type="range"]::-webkit-slider-runnable-track {
            background-color: #d1d5db;
            height: 0.375rem;;
            border-radius: 0.25rem;
        }

        input[type="range"]:focus {
            outline: none;
            box-shadow: none;
        }

        input[type="range"]:focus::-webkit-slider-runnable-track {
            background-color: #d1d5db;
        }

        input[type="range"]::-moz-range-thumb {
            background-color: #6b7280;
            border: none;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
        }

        input[type="range"]::-moz-range-track {
            background-color: #d1d5db;
            height: 0.375rem;
            border-radius: 0.25rem;
        }

        .bg-financial {
            background-image: url('/images/background/financial-literacy-2025.webp');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .bg-spiritual {
            background-image: url('/images/background/attachment-to-God-2025.webp');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* .bg-game-session {
            background-image: url('/images/background/game-session-2025.webp');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        } */
    </style>
    @endpush
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
        @stack('styles')
    </head>
    <body class="font-sans antialiased @yield('body-class') @yield('background-class')">
        <div class="min-h-screen 'bg-gray-100 dark:bg-gray-900'">
            @include('layouts.navigation')
            @stack('custom-style')
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow" style="background-color: rgba(255,255,255,0.8);">
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
    </body>
</html>
