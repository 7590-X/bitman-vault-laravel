<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('descripcion', 'Bitman — Tu bóveda de secretos segura y personal.')">
    <title>@yield('titulo', 'Bitman') — Bóveda de Secretos</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-slate-50 min-h-screen antialiased">

    @yield('contenido')

</body>
</html>
