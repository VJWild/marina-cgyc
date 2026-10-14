<div class="h-full w-full flex flex-col md:flex-row overflow-hidden relative">

    <!-- ÁREA CENTRAL: CABECERA DE FILTROS + SECCIONES CROMÁTICAS -->
    <div class="flex-1 flex flex-col h-full overflow-hidden p-4 md:p-6">

        <!-- Cabecera Operativa -->
        <div class="glass-panel rounded-3xl px-6 py-4 flex flex-col xl:flex-row xl:items-center justify-between gap-4 shrink-0 mb-6">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl md:text-2xl font-bold text-white tracking-tight">Muelles</h1>
                    <span class="text-[10px] uppercase tracking-widest font-bold bg-[#C8102E]/25 text-rose-300 border border-[#C8102E]/40 px-3 py-0.5 rounded-full">
                        Operaciones en Vivo
                    </span>
                </div>
                <p class="text-xs text-blue-200/70 mt-1">Asignación de puestos, despacho de zarpes con destino y control de retornos.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Buscador en tiempo real -->
                <div class="relative w-full sm:w-64">
                    <i class="fas fa-search absolute left-4 top-3.5 text-blue-200/50 text-sm"></i>
                    <input
                        wire:model.live.debounce.200ms="search"
                        type="text"
                        placeholder="Puesto, barco, matrícula o armador..."
                        class="w-full pl-11 pr-4 py-2.5 glass-card rounded-2xl text-xs text-white placeholder-blue-200/45 focus:ring-2 focus:ring-blue-400/40 outline-none"
                    >
                </div>

                <!-- Filtros rápidos de estado -->
                <div class="flex bg-[#061020]/60 p-1.5 rounded-2xl border border-white/15 text-[11px] font-bold uppercase tracking-wider">
                    <button wire:click="$set('statusFilter', 'all')" class="px-3 py-1.5 rounded-xl transition-all {{ $statusFilter === 'all' ? 'btn-apple-navy' : 'text-blue-200/70 hover:text-white' }}">
                        Todos
                    </button>
                    <button wire:click="$set('statusFilter', 'en_marina')" class="px-3 py-1.5 rounded-xl transition-all {{ $statusFilter === 'en_marina' ? 'btn-apple-navy' : 'text-blue-200/70 hover:text-white' }}">
                        En Marina
                    </button>
                    <button wire:click="$set('statusFilter', 'de_viaje')" class="px-3 py-1.5 rounded-xl transition-all {{ $statusFilter === 'de_viaje' ? 'btn-apple-crimson' : 'text-blue-200/70 hover:text-white' }}">
                        En Zarpe
                    </button>
                    <button wire:click="$set('statusFilter', 'free')" class="px-3 py-1.5 rounded-xl transition-all {{ $statusFilter === 'free' ? 'btn-apple-navy' : 'text-blue-200/70 hover:text-white' }}">
                        Libres
                    </button>
                </div>
            </div>
        </div>

        <!-- Notificación Flash de Operaciones -->
        @if (session()->has('status_message'))
            <div class="mb-4 px-5 py-3 rounded-2xl glass-card border border-emerald-400/40 bg-emerald-500/15 text-emerald-200 text-xs font-bold flex items-center justify-between animate-fade-in shrink-0">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-circle-check text-emerald-400 text-sm"></i>
                    <span>{{ session('status_message') }}</span>
                </div>
            </div>
        @endif

        <!-- Listado de Zonas y Tarjetas de Puestos -->
        <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 pb-10 space-y-6">
            @forelse ($filteredZones as $zone)
                @php
                    $isCollapsed = empty($search) && in_array($zone->id, $collapsedZones);
                @endphp
                <div class="glass-panel p-5 md:p-6 rounded-[2rem]">
                    <div wire:click="toggleZone({{ $zone->id }})" class="flex items-center pl-1 cursor-pointer group select-none">
                        <div class="w-3.5 h-3.5 rounded-full mr-3 shadow-md transition-transform group-hover:scale-125" style="background-color: {{ $zone->hex_code }}"></div>
                        <h2 class="text-sm md:text-base font-bold text-white uppercase tracking-widest">Zona {{ $zone->name }}</h2>
                        <span class="ml-auto md:ml-4 text-[10px] font-bold text-blue-200 bg-white/10 px-3 py-1 rounded-full border border-white/15">
                            {{ $zone->slips->count() }} ptos.
                        </span>
                        <div class="ml-3 w-7 h-7 flex items-center justify-center rounded-full bg-white/10 group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-chevron-up text-white text-[10px] transition-transform duration-300 {{ $isCollapsed ? 'rotate-180' : '' }}"></i>
                        </div>
                    </div>

                    @if (!$isCollapsed)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3.5 md:gap-4 mt-5">
                            @foreach ($zone->slips as $slip)
                                @php
                                    $hasBoats = $slip->boats->isNotEmpty();
                                    $isShared = $slip->boats->count() > 1;
                                    $isSelected = $selectedSlipId === $slip->id;
                                @endphp

                                <div
                                    wire:click="selectSlip({{ $slip->id }})"
                                    class="glass-card glass-card-hover p-3 md:p-3.5 rounded-3xl cursor-pointer flex flex-col justify-between items-center text-center min-h-[7.5rem] md:min-h-[8.25rem] relative overflow-hidden {{ $isSelected ? 'ring-2 ring-white bg-white/20 scale-[1.02]' : '' }}"
                                >
                                    @if ($hasBoats)
                                        <div class="absolute top-0 w-full h-1.5" style="background-color: {{ $zone->hex_code }}"></div>

                                        <div class="w-full flex items-center justify-between px-1 pt-0.5 mb-1">
                                            <span class="text-[10px] font-bold text-blue-200/70 tracking-widest">Nº {{ $slip->code }}</span>
                                            @if ($isShared)
                                                <span class="text-[8px] font-bold uppercase bg-blue-500/30 text-blue-200 px-1.5 py-0.5 rounded-md border border-blue-400/30">
                                                    Compartido ({{ $slip->boats->count() }})
                                                </span>
                                            @endif
                                        </div>

                                        <div class="w-full space-y-1.5 my-auto">
                                            @foreach ($slip->boats as $boatInSlip)
                                                @php
                                                    $isBoatSailing = $boatInSlip->navigation_status === 'de_viaje';
                                                @endphp
                                                <div class="w-full px-2.5 py-1.5 rounded-2xl bg-white/10 border {{ $isBoatSailing ? 'border-amber-400/40 bg-amber-500/10' : 'border-white/10' }} flex flex-col items-start text-left">
                                                    <div class="w-full flex items-center justify-between gap-1">
                                                        <span class="font-bold text-[11px] text-white truncate">{{ $boatInSlip->name }}</span>
                                                        <span class="w-2 h-2 rounded-full shrink-0 {{ $isBoatSailing ? 'bg-amber-400' : 'bg-emerald-400' }}"></span>
                                                    </div>
                                                    <div class="w-full flex items-center justify-between text-[8px] text-blue-200/70 font-semibold uppercase mt-0.5">
                                                        <span class="truncate">{{ $boatInSlip->matricula }}</span>
                                                        @if ($isBoatSailing)
                                                            <span class="text-amber-300 font-bold ml-1 truncate">
                                                                <i class="fas fa-location-arrow mr-0.5"></i>{{ $boatInSlip->activeSailing?->destination ?? 'Zarpe' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="absolute top-0 w-full h-1.5 bg-white/15"></div>
                                        <span class="text-[10px] font-bold text-blue-200/50 mt-2 tracking-widest">Nº {{ $slip->code }}</span>
                                        <span class="my-auto font-bold text-[9px] uppercase text-emerald-300 bg-emerald-500/15 px-3 py-1 rounded-full border border-emerald-400/30">
                                            <i class="fas fa-check mr-1"></i>Disponible
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="glass-panel p-12 rounded-[2rem] text-center">
                    <i class="fas fa-ship text-4xl text-blue-200/30 mb-3"></i>
                    <p class="text-sm font-semibold text-blue-100">No se encontraron puestos ni embarcaciones con ese criterio.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- PANEL LATERAL DERECHO: GESTIÓN DE PUESTO, ZARPES Y RETORNOS -->
    @if ($selectedSlip)
        <div wire:click="closeSidePanel" class="fixed inset-0 bg-[#020c1b]/65 backdrop-blur-sm z-40 md:hidden"></div>
    @endif

    <div class="fixed inset-y-0 right-0 z-50 w-[92%] max-w-md md:w-[430px] md:max-w-none transform transition-transform duration-300 ease-in-out {{ $selectedSlip ? 'translate-x-0' : 'translate-x-full md:translate-x-0' }} md:relative p-4 md:p-6 md:pl-0 h-full flex flex-col shrink-0">
        <div class="glass-panel rounded-3xl flex-1 flex flex-col overflow-hidden relative">

            @if (!$selectedSlip)
                <!-- Estado sin selección -->
                <div class="h-full flex flex-col items-center justify-center p-8 text-center">
                    <div class="w-20 h-20 rounded-full glass-card flex items-center justify-center mb-5">
                        <i class="fas fa-hand-pointer text-2xl text-blue-200/60"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1">Visor Operativo de Muelle</h3>
                    <p class="text-xs text-blue-200/65 leading-relaxed">
                        Selecciona cualquier puesto a la izquierda para ver su ficha técnica, autorizar un zarpe, registrar un retorno o asignar embarcaciones.
                    </p>
                </div>
            @else
                <!-- Cabecera del Puesto Seleccionado -->
                <div class="p-6 pb-5 border-b border-white/10 shrink-0 relative z-10">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[10px] uppercase tracking-[0.22em] text-blue-200/60 font-bold">Gestión de Puesto</span>
                        <button wire:click="closeSidePanel" class="md:hidden w-8 h-8 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-end">
                            <span class="text-3xl font-light text-blue-300/50 mr-1 pb-1">#</span>
                            <span class="text-5xl font-bold tracking-tighter text-white">{{ $selectedSlip->code }}</span>
                        </div>
                        <span class="text-[10px] uppercase font-bold px-3.5 py-1.5 rounded-full shadow-md text-white tracking-wider"
                              style="background-color: {{ $selectedSlip->zone->hex_code }}">
                            Zona {{ $selectedSlip->zone->name }}
                        </span>
                    </div>

                    <div class="mt-4 flex items-center justify-between glass-card px-4 py-2 rounded-2xl">
                        <div class="flex items-center">
                            <div class="w-2.5 h-2.5 rounded-full mr-2.5 {{ $selectedSlip->boats->isNotEmpty() ? 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]' : 'bg-slate-400' }}"></div>
                            <span class="font-bold text-xs tracking-wider text-blue-100 uppercase">
                                {{ $selectedSlip->boats->isNotEmpty() ? 'Ocupado (' . $selectedSlip->boats->count() . ')' : 'Muelle Libre' }}
                            </span>
                        </div>
                        @if ($selectedSlip->max_aforo_ft)
                            <span class="text-[10px] font-bold text-blue-200/75 uppercase">Aforo Ref: {{ $selectedSlip->max_aforo_ft }} ft</span>
                        @endif
                    </div>
                </div>

                <!-- Cuerpo del Panel Lateral -->
                <div class="p-6 flex-1 overflow-y-auto custom-scrollbar space-y-6 relative z-10">

                    <!-- 1. EMBARCACIONES ASIGNADAS A ESTE PUESTO -->
                    @if ($selectedSlip->boats->isNotEmpty())
                        <div class="space-y-4">
                            <h3 class="text-blue-200/80 font-bold uppercase tracking-[0.16em] text-[10px] flex items-center">
                                <i class="fas fa-file-invoice mr-2 text-blue-300/60"></i> Ficha Técnica y Control de Viaje
                            </h3>

                            @foreach ($selectedSlip->boats as $boat)
                                @php
                                    $isSailing = $boat->navigation_status === 'de_viaje';
                                    $sailing = $boat->activeSailing;
                                @endphp
                                <div class="glass-card p-5 rounded-3xl space-y-4 border {{ $isSailing ? 'border-amber-400/40' : 'border-white/15' }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <p class="text-[10px] text-blue-200/60 uppercase font-bold tracking-widest">Embarcación</p>
                                            <h4 class="text-xl font-bold text-white tracking-tight">{{ $boat->name }}</h4>
                                        </div>

                                        @if ($isSailing)
                                            <span class="text-[9px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30">
                                                <i class="fas fa-compass mr-1"></i>En Zarpe
                                            </span>
                                        @else
                                            <span class="text-[9px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                                <i class="fas fa-anchor mr-1"></i>En Marina
                                            </span>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="text-[10px] text-blue-200/60 uppercase font-bold tracking-widest">Propietario / Armador</p>
                                        <p class="text-xs font-semibold text-blue-50 mt-0.5">{{ $boat->owner_name }}</p>
                                    </div>

                                    <div class="grid grid-cols-3 gap-2 pt-1">
                                        <div class="bg-white/5 p-2.5 rounded-2xl border border-white/10">
                                            <p class="text-[9px] text-blue-200/60 uppercase font-bold">Matrícula</p>
                                            <p class="text-[11px] font-bold text-white truncate mt-0.5">{{ $boat->matricula }}</p>
                                        </div>
                                        <div class="bg-white/5 p-2.5 rounded-2xl border border-white/10">
                                            <p class="text-[9px] text-blue-200/60 uppercase font-bold">Eslora</p>
                                            <p class="text-[11px] font-bold text-white mt-0.5">{{ $boat->eslora_ft ? number_format($boat->eslora_ft, 0) . ' ft' : 'N/D' }}</p>
                                        </div>
                                        <div class="bg-white/5 p-2.5 rounded-2xl border border-white/10">
                                            <p class="text-[9px] text-blue-200/60 uppercase font-bold">Aforo</p>
                                            <p class="text-[11px] font-bold text-white mt-0.5">{{ $boat->aforo_ft ? number_format($boat->aforo_ft, 0) . ' ft' : 'N/D' }}</p>
                                        </div>
                                    </div>

                                    <!-- Detalles del Zarpe Activo si está de viaje -->
                                    @if ($isSailing && $sailing)
                                        <div class="p-3.5 rounded-2xl bg-amber-500/15 border border-amber-400/30 text-xs space-y-1">
                                            <div class="flex items-center justify-between text-amber-200 font-bold">
                                                <span><i class="fas fa-route mr-1.5"></i>Destino: {{ $sailing->destination }}</span>
                                            </div>
                                            <p class="text-[11px] text-amber-100/80">
                                                Salida: {{ $sailing->departed_at->format('d/m/Y H:i') }}
                                            </p>
                                            @if ($sailing->expected_return_at)
                                                <p class="text-[11px] text-amber-100/80">
                                                    Retorno est.: {{ $sailing->expected_return_at->format('d/m/Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Botonera Operativa de la Embarcación -->
                                    <div class="pt-2 space-y-2">
                                        @if ($isSailing)
                                            <button
                                                wire:click="openReturnModal({{ $boat->id }})"
                                                class="btn-apple-navy w-full py-3 px-4 rounded-2xl text-[11px] font-bold uppercase tracking-widest flex items-center justify-center gap-2"
                                            >
                                                <i class="fas fa-anchor"></i> Registrar Retorno a Marina
                                            </button>
                                        @else
                                            <button
                                                wire:click="openSailingModal({{ $boat->id }})"
                                                class="btn-apple-crimson w-full py-3 px-4 rounded-2xl text-[11px] font-bold uppercase tracking-widest flex items-center justify-center gap-2"
                                            >
                                                <i class="fas fa-compass"></i> Autorizar Zarpe de Viaje
                                            </button>
                                        @endif

                                        <div class="grid grid-cols-2 gap-2">
                                            <button
                                                wire:click="openRelocateModal({{ $boat->id }})"
                                                class="py-2.5 px-3 rounded-xl glass-card hover:bg-white/15 text-[10px] font-bold uppercase tracking-wider text-blue-100 flex items-center justify-center gap-1.5 transition-all"
                                            >
                                                <i class="fas fa-arrows-left-right"></i> Cambiar Puesto
                                            </button>
                                            <button
                                                wire:click="releaseBoat({{ $boat->id }})"
                                                class="py-2.5 px-3 rounded-xl bg-rose-500/15 hover:bg-rose-500/30 border border-rose-400/30 text-[10px] font-bold uppercase tracking-wider text-rose-200 flex items-center justify-center gap-1.5 transition-all"
                                            >
                                                <i class="fas fa-link-slash"></i> Liberar Puesto
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- 2. BUSCADOR PARA AMARRAR / ASIGNAR EMBARCACIÓN EN ESTE PUESTO -->
                    <div class="pt-2 border-t border-white/10">
                        <h3 class="text-blue-200/80 font-bold mb-3 uppercase tracking-[0.15em] text-[10px] flex items-center">
                            <i class="fas fa-anchor mr-2 text-blue-300/60"></i>
                            {{ $selectedSlip->boats->isNotEmpty() ? 'Amarrar Embarcación Adicional' : 'Asignar Embarcación a este Muelle' }}
                        </h3>

                        <div class="relative mb-4">
                            <i class="fas fa-search absolute left-4 top-3.5 text-blue-200/50 text-xs"></i>
                            <input
                                wire:model.live.debounce.200ms="assignSearch"
                                type="text"
                                placeholder="Buscar barco por nombre o matrícula..."
                                class="w-full pl-10 pr-4 py-2.5 glass-card rounded-2xl text-xs text-white placeholder-blue-200/45 outline-none focus:ring-2 focus:ring-blue-400/40"
                            >
                        </div>

                        <div class="space-y-2.5 max-h-64 overflow-y-auto custom-scrollbar pr-1">
                            @foreach ($assignableBoats as $boatItem)
                                @if ($boatItem->slip_id !== $selectedSlip->id)
                                    <div class="glass-card p-3.5 rounded-2xl flex items-center justify-between gap-2 hover:bg-white/10 transition-all">
                                        <div class="truncate">
                                            <p class="font-bold text-xs text-white truncate">{{ $boatItem->name }}</p>
                                            <p class="text-[10px] text-blue-200/60 uppercase">{{ $boatItem->matricula }}</p>
                                            <div class="mt-1 flex items-center gap-1.5">
                                                @if ($boatItem->slip)
                                                    <span class="text-[9px] font-bold uppercase bg-white/10 text-blue-200 px-2 py-0.5 rounded-md">
                                                        Puesto #{{ $boatItem->slip->code }}
                                                    </span>
                                                @else
                                                    <span class="text-[9px] font-bold uppercase bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded-md">
                                                        Sin Puesto
                                                    </span>
                                                @endif

                                                @if ($boatItem->navigation_status === 'de_viaje')
                                                    <span class="text-[9px] font-bold uppercase bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded-md">
                                                        En Zarpe
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <button
                                            wire:click="initiateAssignBoat({{ $boatItem->id }})"
                                            class="btn-apple-navy px-3.5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-wider shrink-0"
                                        >
                                            Asignar
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>

    <!-- MODAL 1: CONFIRMAR REUBICACIÓN DESDE OTRO PUESTO -->
    @if ($showMoveConfirmModal && $pendingBoat && $selectedSlip)
        <div class="fixed inset-0 bg-[#020c1b]/75 backdrop-blur-md z-[70] flex items-center justify-center p-4">
            <div class="glass-panel rounded-[2rem] p-7 max-w-md w-full animate-fade-in">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-300">
                        <i class="fas fa-exchange-alt text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Confirmar Reubicación</h3>
                        <p class="text-xs text-blue-200/70">Cambio de muelle operativo</p>
                    </div>
                </div>

                <p class="text-xs text-blue-100/90 leading-relaxed mb-6">
                    La embarcación <strong class="text-white">{{ $pendingBoat->name }}</strong> ocupa actualmente el puesto
                    <strong class="text-white">#{{ $pendingBoat->slip?->code }}</strong>.<br><br>
                    ¿Autorizas transferirla al puesto <strong class="text-emerald-300">#{{ $selectedSlip->code }}</strong>?
                </p>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showMoveConfirmModal', false)" class="px-5 py-2.5 rounded-xl glass-card text-xs font-bold text-blue-200 hover:text-white">
                        Cancelar
                    </button>
                    <button wire:click="confirmMoveBoat" class="btn-apple-crimson px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider">
                        Confirmar Mudanza
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: CAMBIAR DE PUESTO (SELECTOR DE MUELLE DESTINO) -->
    @if ($showRelocateModal && $relocatingBoat)
        <div class="fixed inset-0 bg-[#020c1b]/75 backdrop-blur-md z-[70] flex items-center justify-center p-4">
            <div class="glass-panel rounded-[2rem] p-7 max-w-md w-full animate-fade-in">
                <h3 class="text-lg font-bold text-white mb-1">Cambiar de Puesto</h3>
                <p class="text-xs text-blue-200/70 mb-5">Seleccione el nuevo muelle para <strong class="text-white">{{ $relocatingBoat->name }}</strong></p>

                <div class="mb-6">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-2">Puesto Destino</label>
                    <select wire:model="targetSlipId" class="w-full py-3 px-4 glass-card rounded-2xl text-sm text-white bg-[#0A192F] border border-white/20 outline-none">
                        @foreach ($allSlips as $s)
                            <option value="{{ $s->id }}" class="bg-[#0A192F] text-white">
                                Puesto #{{ $s->code }} — Zona {{ $s->zone->name }} ({{ $s->status === 'free' ? 'Libre' : 'Ocupado' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showRelocateModal', false)" class="px-5 py-2.5 rounded-xl glass-card text-xs font-bold text-blue-200">
                        Cancelar
                    </button>
                    <button wire:click="executeRelocateBoat" class="btn-apple-navy px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider">
                        Guardar Cambio
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: DESPACHAR ZARPE CON DESTINO -->
    @if ($showSailingModal && $sailingBoat)
        <div class="fixed inset-0 bg-[#020c1b]/75 backdrop-blur-md z-[70] flex items-center justify-center p-4">
            <div class="glass-panel rounded-[2rem] p-7 max-w-md w-full animate-fade-in">
                <div class="flex items-center gap-3.5 mb-5">
                    <div class="w-11 h-11 rounded-2xl bg-[#C8102E]/25 border border-[#C8102E]/40 flex items-center justify-center text-rose-300">
                        <i class="fas fa-compass text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Despacho de Zarpe</h3>
                        <p class="text-xs text-blue-200/70">{{ $sailingBoat->name }} ({{ $sailingBoat->matricula }})</p>
                    </div>
                </div>

                <form wire:submit="registerSailing" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">
                            Destino de Navegación *
                        </label>
                        <input
                            wire:model="destination"
                            type="text"
                            required
                            placeholder="Ej: Los Roques, La Tortuga, Carenero..."
                            class="w-full py-3 px-4 glass-card rounded-2xl text-sm text-white placeholder-blue-200/40 outline-none focus:ring-2 focus:ring-[#C8102E]/50"
                        >
                        @error('destination')
                        <span class="text-xs text-rose-400 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">
                            Fecha / Hora Estimada de Retorno (Opcional)
                        </label>
                        <input
                            wire:model="expectedReturnAt"
                            type="datetime-local"
                            class="w-full py-3 px-4 glass-card rounded-2xl text-sm text-white outline-none focus:ring-2 focus:ring-blue-400/40"
                        >
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showSailingModal', false)" class="px-5 py-2.5 rounded-xl glass-card text-xs font-bold text-blue-200">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-apple-crimson px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider">
                            <i class="fas fa-location-arrow mr-1.5"></i> Autorizar Zarpe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 4: REGISTRAR RETORNO A LA MARINA -->
    @if ($showReturnModal && $returningBoat)
        <div class="fixed inset-0 bg-[#020c1b]/75 backdrop-blur-md z-[70] flex items-center justify-center p-4">
            <div class="glass-panel rounded-[2rem] p-7 max-w-md w-full animate-fade-in">
                <div class="flex items-center gap-3.5 mb-5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center text-emerald-300">
                        <i class="fas fa-anchor text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Registrar Retorno</h3>
                        <p class="text-xs text-blue-200/70">Arribo de {{ $returningBoat->name }}</p>
                    </div>
                </div>

                @if ($returningBoat->activeSailing)
                    <div class="p-3.5 rounded-2xl glass-card mb-4 text-xs text-blue-100">
                        Procedencia: <strong class="text-white">{{ $returningBoat->activeSailing->destination }}</strong><br>
                        Zarpó el: {{ $returningBoat->activeSailing->departed_at->format('d/m/Y H:i') }}
                    </div>
                @endif

                <div class="mb-6">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-2">
                        Puesto de Amarre al Retornar
                    </label>
                    <select wire:model="returnSlipId" class="w-full py-3 px-4 glass-card rounded-2xl text-sm text-white bg-[#0A192F] border border-white/20 outline-none">
                        @foreach ($allSlips as $s)
                            <option value="{{ $s->id }}" class="bg-[#0A192F] text-white">
                                Puesto #{{ $s->code }} — Zona {{ $s->zone->name }} {{ $returningBoat->slip_id === $s->id ? '(Su puesto asignado)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showReturnModal', false)" class="px-5 py-2.5 rounded-xl glass-card text-xs font-bold text-blue-200">
                        Cancelar
                    </button>
                    <button wire:click="registerReturn" class="btn-apple-navy px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider">
                        <i class="fas fa-check mr-1.5"></i> Confirmar Retorno
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
