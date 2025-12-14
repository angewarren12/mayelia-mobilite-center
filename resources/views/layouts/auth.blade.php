<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Connexion - Mayelia Mobilité Center')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="{{ asset('js/tailwind.js') }}"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'mayelia': {
                            50: '#f2faf5',
                            100: '#e6f4ec',
                            200: '#c0e4cf',
                            300: '#9ad3b2',
                            400: '#4eb279',
                            500: '#02913F',
                            600: '#028339',
                            700: '#01662c',
                            800: '#014920',
                            900: '#012c13',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome Local -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    
    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="font-inter antialiased">
    <!-- Main Content (Full Screen) -->
    @yield('content')

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
