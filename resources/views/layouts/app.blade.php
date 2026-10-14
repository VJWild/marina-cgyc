<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Panel de Capitanía — Marina CGYC' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logocgyc.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-full overflow-hidden bg-navy-mesh text-white antialiased" x-data="{ sidebarOpen: false }">

<!-- Backdrop Móvil para la Sidebar -->
<div
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-[#020c1b]/75 backdrop-blur-sm z-40 lg:hidden"
    style="display: none;"
></div>

<div class="flex h-screen w-full overflow-hidden">

    <!-- SIDEBAR DE NAVEGACIÓN (Navy Profundo + Glassmorphism) -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 w-72 transform transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 flex flex-col p-4 lg:p-5 lg:pr-0 shrink-0"
    >
        <div class="glass-panel rounded-[2rem] flex-1 flex flex-col justify-between p-5 overflow-hidden relative">
            <!-- Luz decorativa interna -->
            <div class="absolute -left-16 -top-16 w-44 h-44 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <div>
                <!-- Logo y Cabecera Sidebar -->
                <div class="flex items-center justify-between pb-6 mb-6 border-b border-white/10">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 group">
                        <img src="{{ asset('img/logocgyc.png') }}" alt="Logo CGYC" class="h-14 w-auto object-contain drop-shadow-md transition-transform duration-300 group-hover:scale-105">
                        <div>
                            <h2 class="text-base font-bold text-white tracking-tight leading-none">Marina CGYC</h2>
                            <span class="text-[9px] uppercase tracking-[0.22em] text-blue-200/70 font-semibold block mt-1">Capitanía</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden w-8 h-8 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <!-- Menú Principal -->
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-200/50 px-3 mb-3">Módulos Operativos</p>
                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3.5 px-4 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider transition-all duration-300 {{ request()->routeIs('dashboard') ? 'btn-apple-crimson shadow-lg' : 'text-blue-100/75 hover:text-white hover:bg-white/10 hover:translate-x-1' }}">
                        <i class="fas fa-chart-pie text-sm w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('slips.index') }}"
                       class="flex items-center gap-3.5 px-4 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider transition-all duration-300 {{ request()->routeIs('slips.*') ? 'btn-apple-crimson shadow-lg' : 'text-blue-100/75 hover:text-white hover:bg-white/10 hover:translate-x-1' }}">
                        <i class="fas fa-anchor text-sm w-5 text-center"></i>
                        <span>Control de Muelles</span>
                    </a>

                    <a href="{{ route('boats.index') }}"
                       class="flex items-center gap-3.5 px-4 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider transition-all duration-300 {{ request()->routeIs('boats.*') ? 'btn-apple-crimson shadow-lg' : 'text-blue-100/75 hover:text-white hover:bg-white/10 hover:translate-x-1' }}">
                        <i class="fas fa-ship text-sm w-5 text-center"></i>
                        <span>Embarcaciones</span>
                    </a>

                    <a href="{{ route('map.index') }}"
                       class="flex items-center gap-3.5 px-4 py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider transition-all duration-300 {{ request()->routeIs('map.*') ? 'btn-apple-crimson shadow-lg' : 'text-blue-100/75 hover:text-white hover:bg-white/10 hover:translate-x-1' }}">
                        <i class="fas fa-map-location-dot text-sm w-5 text-center"></i>
                        <span>Plano General</span>
                    </a>
                </nav>
            </div>

            <!-- Pie de Sidebar: Usuario Activo y Cerrar Sesión -->
            <div class="pt-5 border-t border-white/10 space-y-3">
                <a href="{{ route('welcome') }}" target="_blank" class="flex items-center justify-between px-4 py-2.5 rounded-xl glass-card hover:bg-white/15 text-[11px] font-semibold text-blue-200 transition-all">
                    <span><i class="fas fa-eye mr-2 text-blue-300"></i>Vista Invitados</span>
                    <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                </a>

                <div class="glass-card p-3.5 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-xl bg-[#C8102E] text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-md">
                            {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 2)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Operador' }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-blue-200/60 font-semibold">{{ auth()->user()->role ?? 'admin' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Cerrar Sesión" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-[#C8102E] text-blue-200 hover:text-white flex items-center justify-center transition-all">
                            <i class="fas fa-power-off text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Barra Superior Móvil (Solo visible en teléfonos/tablets) -->
        <header class="lg:hidden px-4 pt-4 shrink-0">
            <div class="glass-panel rounded-2xl px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="w-9 h-9 rounded-xl bg-white/10 text-white flex items-center justify-center">
                        <i class="fas fa-bars"></i>
                    </button>
                    <img src="{{ asset('img/logocgyc.png') }}" alt="Logo CGYC" class="h-10 w-auto object-contain">
                    <span class="font-bold text-sm tracking-tight text-white">Marina CGYC</span>
                </div>
                <span class="text-[10px] uppercase tracking-widest font-bold bg-white/10 text-blue-200 px-3 py-1 rounded-full">
                        Capitanía
                    </span>
            </div>
        </header>

        <!-- Slot de la Vista Activa -->
        <main class="flex-1 overflow-hidden">
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
