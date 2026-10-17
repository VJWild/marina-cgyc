<x-app-layout>
    <div class="h-full w-full overflow-y-auto custom-scrollbar p-3 sm:p-4 md:p-6 space-y-6">

        <!-- CABECERA DEL DASHBOARD -->
        <div class="glass-panel rounded-3xl px-5 sm:px-6 py-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Bienvenido</h1>
                    <span class="inline-flex items-center whitespace-nowrap shrink-0 text-[10px] uppercase tracking-widest font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 px-3 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-ping"></span>En Línea
                    </span>
                </div>
                <p class="text-xs text-blue-200/70 mt-1">
                    Resumen de Operaciones de la Marina
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('slips.index') }}" class="btn-apple-navy px-4 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-wider inline-flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-anchor"></i> Consultar Muelles
                </a>
                <a href="{{ route('boats.index') }}" class="btn-apple-crimson px-4 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-wider inline-flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-ship"></i> Ver Embarcaciones
                </a>
            </div>
        </div>

        <!-- 1. TARJETAS DE MÉTRICAS (KPIs EN TIEMPO REAL) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            <!-- KPI 1: Embarcaciones en la Marina -->
            <div class="glass-panel p-5 rounded-3xl glass-card-hover relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-blue-200/70">En la Marina</p>
                        <p class="text-3xl sm:text-4xl font-bold text-white mt-2 tracking-tight">{{ $boatsInMarina }}</p>
                        <p class="text-[11px] text-emerald-300 font-semibold mt-2 flex items-center gap-1 whitespace-nowrap">
                            <i class="fas fa-anchor"></i> De {{ $totalBoats }} registradas en total
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-300 shrink-0">
                        <i class="fas fa-ship text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- KPI 2: Embarcaciones por Fuera (En Zarpe) -->
            <div class="glass-panel p-5 rounded-3xl glass-card-hover relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-blue-200/70">Por Fuera (En Zarpe)</p>
                        <p class="text-3xl sm:text-4xl font-bold text-white mt-2 tracking-tight">{{ $boatsSailing }}</p>
                        <p class="text-[11px] text-amber-300 font-semibold mt-2 flex items-center gap-1 whitespace-nowrap">
                            <i class="fas fa-compass"></i> Navegando actualmente
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center text-amber-300 shrink-0">
                        <i class="fas fa-location-arrow text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- KPI 3: Disponibilidad de Puestos -->
            <div class="glass-panel p-5 rounded-3xl glass-card-hover relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-blue-200/70">Puestos Disponibles</p>
                        <div class="flex items-baseline gap-2 mt-2">
                            <p class="text-3xl sm:text-4xl font-bold text-white tracking-tight">{{ $freeSlips }}</p>
                            <span class="text-xs font-semibold text-blue-200/60">/ {{ $totalSlips }} muelles</span>
                        </div>
                        <p class="text-[11px] text-blue-200 font-semibold mt-2 flex items-center gap-1 whitespace-nowrap">
                            <i class="fas fa-check-circle text-blue-400"></i> {{ $occupiedSlips }} puestos ocupados
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-300 shrink-0">
                        <i class="fas fa-water text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- KPI 4: Alertas de Documentos (Licencia y Radio) -->
            <div class="glass-panel p-5 rounded-3xl glass-card-hover relative overflow-hidden border {{ $expiringDocuments->count() > 0 ? 'border-[#C8102E]/60' : 'border-white/15' }}">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-rose-300">Alertas Documentales</p>
                        <p class="text-3xl sm:text-4xl font-bold text-white mt-2 tracking-tight">{{ $expiringDocuments->count() }}</p>
                        <p class="text-[11px] {{ $expiringDocuments->count() > 0 ? 'text-rose-300' : 'text-emerald-300' }} font-semibold mt-2 flex items-center gap-1 whitespace-nowrap">
                            <i class="fas fa-file-shield"></i> Vencidos o próximos (30 días)
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-[#C8102E]/25 border border-[#C8102E]/50 flex items-center justify-center text-rose-300 shrink-0">
                        <i class="fas fa-bell text-xl"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- 2. SECCIÓN MEDIA: ALERTAS DE DOCUMENTOS + EMBARCACIONES EN ZARPE -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            <!-- Panel Izquierdo: Alertas de Vencimiento (Licencia de Navegación y Certificado de Radio) -->
            <div class="glass-panel rounded-[2rem] p-5 sm:p-6 flex flex-col">
                <div class="flex items-center justify-between gap-2 pb-4 mb-4 border-b border-white/10">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-triangle-exclamation text-[#C8102E]"></i> Documentos Próximos a Vencer / Vencidos
                        </h2>
                        <p class="text-[11px] text-blue-200/65 mt-0.5">Hay documentos proximos a vencer que requieren atención</p>
                    </div>
                    <a href="{{ route('boats.index') }}" class="text-[11px] font-bold text-blue-200 hover:text-white underline whitespace-nowrap shrink-0">
                        Ver todos
                    </a>
                </div>

                <div class="flex-1 overflow-x-auto custom-scrollbar">
                    @if ($expiringDocuments->isNotEmpty())
                        <table class="w-full min-w-[520px] text-left border-collapse text-xs">
                            <thead>
                            <tr class="text-[10px] font-bold uppercase tracking-wider text-blue-200/60 border-b border-white/10 whitespace-nowrap">
                                <th class="pb-3 pr-3">Embarcación</th>
                                <th class="pb-3 px-3">Certificado / N°</th>
                                <th class="pb-3 px-3 text-center">Vencimiento</th>
                                <th class="pb-3 pl-3 text-right">Estado</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                            @foreach ($expiringDocuments as $doc)
                                @php
                                    $daysLeft = (int) now()->startOfDay()->diffInDays($doc->expires_at->startOfDay(), false);
                                    $isExpired = $daysLeft < 0;
                                @endphp
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-3 pr-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            @if ($doc->boat->slip)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white shrink-0"
                                                      style="background-color: {{ $doc->boat->slip->zone->hex_code }}">
                                                        #{{ $doc->boat->slip->code }}
                                                    </span>
                                            @endif
                                            <div>
                                                <p class="font-bold text-white">{{ $doc->boat->name }}</p>
                                                <p class="text-[10px] text-blue-200/60 uppercase">{{ $doc->boat->matricula }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <p class="font-semibold text-blue-100">{{ $doc->document_type }}</p>
                                        <p class="text-[10px] text-blue-200/60">{{ $doc->document_number }}</p>
                                    </td>
                                    <td class="py-3 px-3 text-center font-bold text-white whitespace-nowrap">
                                        {{ $doc->expires_at->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 pl-3 text-right whitespace-nowrap">
                                        @if ($isExpired)
                                            <span class="inline-flex items-center justify-center whitespace-nowrap px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-[#C8102E] text-white shadow-sm">
                                                    Vencido ({{ abs($daysLeft) }}d)
                                                </span>
                                        @else
                                            <span class="inline-flex items-center justify-center whitespace-nowrap px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-500/25 text-amber-300 border border-amber-400/40">
                                                    En {{ $daysLeft }} días
                                                </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="py-10 text-center text-blue-200/60">
                            <i class="fas fa-file-circle-check text-3xl text-emerald-400/70 mb-2 block"></i>
                            <p class="text-xs font-semibold text-blue-100">Todos los certificados registrados se encuentran al día.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Panel Derecho: Embarcaciones por Fuera (Bitácora de Zarpes Activos) -->
            <div class="glass-panel rounded-[2rem] p-5 sm:p-6 flex flex-col">
                <div class="flex items-center justify-between gap-2 pb-4 mb-4 border-b border-white/10">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-compass text-amber-400"></i> Embarcaciones por Fuera (En Zarpe)
                        </h2>
                        <p class="text-[11px] text-blue-200/65 mt-0.5">Monitoreo de viajes activos, destinos y retorno estimado</p>
                    </div>
                    <a href="{{ route('slips.index') }}" class="text-[11px] font-bold text-blue-200 hover:text-white underline whitespace-nowrap shrink-0">
                        Registrar Zarpe
                    </a>
                </div>

                <div class="flex-1 overflow-x-auto custom-scrollbar">
                    @if ($activeSailings->isNotEmpty())
                        <table class="w-full min-w-[500px] text-left border-collapse text-xs">
                            <thead>
                            <tr class="text-[10px] font-bold uppercase tracking-wider text-blue-200/60 border-b border-white/10 whitespace-nowrap">
                                <th class="pb-3 pr-3">Embarcación</th>
                                <th class="pb-3 px-3">Destino</th>
                                <th class="pb-3 px-3">Fecha Salida</th>
                                <th class="pb-3 pl-3 text-right">Muelle Origen</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                            @foreach ($activeSailings as $sailing)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-3 pr-3 whitespace-nowrap">
                                        <p class="font-bold text-white">{{ $sailing->boat->name }}</p>
                                        <p class="text-[10px] text-blue-200/60 uppercase">{{ $sailing->boat->matricula }}</p>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-400/30">
                                                <i class="fas fa-location-arrow text-[9px]"></i>
                                                <span>{{ $sailing->destination }}</span>
                                            </span>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap text-blue-100">
                                        {{ $sailing->departed_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 pl-3 text-right whitespace-nowrap">
                                        @if ($sailing->originSlip)
                                            <span class="inline-flex items-center justify-center whitespace-nowrap px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/10 text-white">
                                                    Puesto #{{ $sailing->originSlip->code }}
                                                </span>
                                        @else
                                            <span class="text-blue-200/50">N/D</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="py-10 text-center text-blue-200/60">
                            <i class="fas fa-anchor text-3xl text-blue-300/40 mb-2 block"></i>
                            <p class="text-xs font-semibold text-blue-100">No hay embarcaciones fuera de la marina en este momento.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- 3. VISOR INTEGRADO DEL MAPA DE SECCIONES Y OCUPACIÓN POR ZONA -->
        <div class="glass-panel rounded-[2rem] p-5 sm:p-6" x-data="{ mapZoom: 100 }">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 mb-5 border-b border-white/10">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-white tracking-tight">Plano Satelital</h2>
                    <p class="text-xs text-blue-200/70">Distribución de los puestos de la Marina Caraballeda Golf & Yacht Club</p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button @click="mapZoom = Math.max(60, mapZoom - 20)" class="btn-apple-navy w-9 h-9 rounded-xl flex items-center justify-center text-xs">
                        <i class="fas fa-magnifying-glass-minus"></i>
                    </button>
                    <span class="px-3 py-1.5 rounded-xl glass-card text-xs font-bold text-white min-w-[60px] text-center" x-text="mapZoom + '%'"></span>
                    <button @click="mapZoom = Math.min(160, mapZoom + 20)" class="btn-apple-navy w-9 h-9 rounded-xl flex items-center justify-center text-xs">
                        <i class="fas fa-magnifying-glass-plus"></i>
                    </button>
                    <a href="{{ route('map.index') }}" class="btn-apple-crimson px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-wider whitespace-nowrap ml-1">
                        <i class="fas fa-expand mr-1.5"></i>Pantalla Completa
                    </a>
                </div>
            </div>

            <!-- Píldoras de Ocupación de las 8 Zonas -->
            <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-8 gap-2.5 mb-5">
                @foreach ($zones as $z)
                    @php
                        $totalZ = $z->slips->count();
                        $occupiedZ = $z->slips->filter(fn($s) => $s->boats->isNotEmpty())->count();
                    @endphp
                    <a href="{{ route('slips.index') }}" class="glass-card glass-card-hover px-3.5 py-2.5 rounded-2xl flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $z->hex_code }}"></span>
                            <span class="text-[11px] font-bold text-white uppercase truncate">{{ $z->name }}</span>
                        </div>
                        <span class="text-[11px] font-bold text-blue-200 whitespace-nowrap shrink-0">{{ $occupiedZ }}/{{ $totalZ }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Contenedor del Plano -->
            <div class="glass-card rounded-3xl p-3 sm:p-4 overflow-auto custom-scrollbar max-h-[540px] flex items-center justify-center">
                <img
                    src="{{ asset('img/MAPA SATELITAL CGYC.jpg') }}"
                    alt="Plano de Secciones Marina CGYC"
                    :style="'width: ' + mapZoom + '%; max-width: none;'"
                    class="rounded-2xl shadow-2xl transition-all duration-200 select-none"
                >
            </div>
        </div>

    </div>
</x-app-layout>
