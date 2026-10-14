<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso Capitanía — Marina CGYC</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logocgyc.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#0A192F] text-white min-h-screen flex flex-col justify-center items-center p-4 relative overflow-hidden select-none">

<!-- Capas de Fondo Animado (Luces Oceánicas y Carmesí) -->
<div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
    <div class="orb-navy-1 absolute -top-24 -left-24 w-96 h-96 md:w-[32rem] md:h-[32rem] rounded-full bg-blue-600/25 blur-[110px]"></div>
    <div class="orb-navy-2 absolute top-1/3 -right-28 w-96 h-96 md:w-[30rem] md:h-[30rem] rounded-full bg-sky-500/20 blur-[120px]"></div>
    <div class="orb-crimson absolute -bottom-28 left-1/4 w-80 h-80 md:w-[26rem] md:h-[26rem] rounded-full bg-[#C8102E]/20 blur-[110px]"></div>
</div>

<!-- Tarjeta Glassmorphism de Login -->
<div class="w-full max-w-md glass-panel rounded-[2.25rem] p-8 md:p-10 relative z-10 animate-fade-in">
    <div class="flex flex-col items-center text-center mb-8">
        <a href="{{ route('welcome') }}" class="transition-transform duration-300 hover:scale-105">
            <img src="{{ asset('img/logocgyc.png') }}" alt="Logo CGYC" class="h-28 md:h-32 w-auto object-contain drop-shadow-[0_10px_20px_rgba(0,0,0,0.45)] mb-4">
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Bienvenido</h1>
        <p class="text-[11px] uppercase tracking-[0.25em] text-blue-200/70 font-semibold mt-1.5">Marina Caraballeda Golf & Yacht Club</p>
    </div>

    {{ $slot }}

    <div class="mt-7 pt-5 border-t border-white/10 text-center">
        <a href="{{ route('welcome') }}" class="inline-flex items-center text-xs font-semibold text-blue-200/80 hover:text-white transition-all duration-300 hover:-translate-y-0.5">
            <i class="fas fa-arrow-left mr-2 text-[#C8102E]"></i>Volver al portal público de invitados
        </a>
    </div>
</div>
</body>
</html>
