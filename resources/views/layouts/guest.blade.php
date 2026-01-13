<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Customer Request System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(120deg, #f0f4ff, #f8fafc);
        }

        .auth-card {
            max-width: 420px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased min-h-screen">

    {{-- Page Content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

</body>
</html>
