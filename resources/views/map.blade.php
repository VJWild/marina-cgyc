<x-app-layout>
    <div class="h-full w-full flex flex-col p-4 md:p-6 overflow-y-auto md:overflow-hidden" x-data="{ zoom: 100 }">

        <!-- Cabecera y Leyenda de Ocupación por Zona -->
        <div class="glass-panel rounded-3xl px-5 py-4 mb-4 shrink-0 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <div>
                <h1 class="text-lg md:text-2xl font-bold text-white tracking-tight">Plano General de la Marina</h1>
                <p class="text-xs text-blue-200/70">Distribución física de muelles y resumen de ocupación por zona cromática.</p>
            </div>

            <!-- Controles de Zoom -->
            <div class="flex items-center gap-2 self-start xl:self-auto">
                <button type="button" @click="zoom = Math.max(60, zoom - 20)" class="btn-apple-navy w-9 h-9 rounded-xl flex items-center justify-center text-xs">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="px-3 py-1.5 rounded-xl glass-card text-xs font-bold text-white min-w-[64px] text-center" x-text="zoom + '%'">100%</span>
                <button type="button" @click="zoom = Math.min(180, zoom + 20)" class="btn-apple-navy w-9 h-9 rounded-xl flex items-center justify-center text-xs">
                    <i class="fas fa-plus"></i>
                </button>
                <button type="button" @click="zoom = 100" class="px-3.5 py-2 rounded-xl glass-card hover:bg-white/15 text-[11px] font-bold uppercase tracking-wider text-blue-200">
                    Restablecer
                </button>
            </div>
        </div>

        <!-- Resumen Rápido de las 8 Zonas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-8 gap-2 mb-4 shrink-0">
            @foreach ($zones as $z)
                @php
                    $total = $z->slips->count();
                    $occupied = $z->slips->filter(fn($s) => $s->boats->isNotEmpty())->count();
                @endphp
                <div class="glass-card px-3 py-2 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-2 truncate">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $z->hex_code }}"></span>
                        <span class="text-[11px] font-bold text-white uppercase truncate">{{ $z->name }}</span>
                    </div>
                    <span class="text-[11px] font-bold text-blue-200 ml-1">{{ $occupied }}/{{ $total }}</span>
                </div>
            @endforeach
        </div>

        <!-- Visor del Plano -->
        <div class="flex-1 min-h-[300px] glass-panel rounded-3xl p-3 md:p-4 overflow-auto custom-scrollbar flex relative">
            <img
                src="{{ asset('img/MAPA SATELITAL CGYC.jpg') }}"
                alt="Plano de Secciones Marina CGYC"
                :style="'width: ' + zoom + '%; max-width: none;'"
                class="rounded-2xl shadow-2xl transition-all duration-200 select-none m-auto"
            >
        </div>
    </div>
</x-app-layout>
