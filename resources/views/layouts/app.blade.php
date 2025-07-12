<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mon Application')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    @auth
        @include('partials.navigation')
        @include('partials.sidebar')
    @endauth
    <div class="pt-16 md:ml-64 transition-all">
        <main class="container mx-auto py-8">
            @yield('content')
        </main>
    </div>
</body>
</html>