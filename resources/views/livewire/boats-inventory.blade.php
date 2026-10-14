<div class="h-full w-full flex flex-col p-3 sm:p-4 md:p-6 overflow-y-auto md:overflow-hidden custom-scrollbar">

    <!-- CABECERA Y CONTROLES SUPERIORES -->
    <div class="glass-panel rounded-3xl px-4 sm:px-6 py-4 mb-4 shrink-0 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-white tracking-tight truncate">
                    Directorio Maestro de Embarcaciones
                </h1>
                <span class="inline-flex items-center whitespace-nowrap shrink-0 text-[10px] uppercase tracking-widest font-bold bg-white/10 text-blue-200 border border-white/15 px-3 py-0.5 rounded-full">
                    {{ $counts['all'] }} Activas
                </span>
            </div>
            <p class="text-xs text-blue-200/70 mt-1">
                Control de armadores, acciones de socios, dimensiones técnicas y certificados INEA.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
            <div class="relative w-full sm:w-72 lg:w-80">
                <i class="fas fa-search absolute left-4 top-3.5 text-blue-200/50 text-sm pointer-events-none"></i>
                <input
                    wire:model.live.debounce.200ms="search"
                    type="text"
                    placeholder="Barco, matrícula, socio o acción (P-040)..."
                    class="w-full pl-11 pr-4 py-2.5 glass-card rounded-2xl text-xs text-white placeholder-blue-200/45 focus:ring-2 focus:ring-blue-400/40 outline-none"
                >
            </div>

            <button
                wire:click="openCreateModal"
                class="btn-apple-crimson px-5 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-wider inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0"
            >
                <i class="fas fa-plus"></i> Añadir Embarcación
            </button>
        </div>
    </div>

    <!-- PESTAÑAS DE FILTRO RÁPIDO (Deslizables en móvil / zoom alto) -->
    <div class="flex items-center gap-2 mb-4 pb-1 overflow-x-auto custom-scrollbar shrink-0">
        <button wire:click="$set('filterStatus', 'all')" class=" whitespace-nowrap shrink-0 px-4 py-2 rounded-2xl text-xs font-bold uppercase tracking-wider transition-all {{ $filterStatus === 'all' ? 'btn-apple-navy' : 'glass-card text-blue-200/75 hover:text-white' }}">
            Todas ({{ $counts['all'] }})
        </button>
        <button wire:click="$set('filterStatus', 'en_marina')" class="whitespace-nowrap shrink-0 px-4 py-2 rounded-2xl text-xs font-bold uppercase tracking-wider transition-all {{ $filterStatus === 'en_marina' ? 'btn-apple-navy' : 'glass-card text-blue-200/75 hover:text-white' }}">
            <i class="fas fa-anchor mr-1.5 text-emerald-400"></i>En Marina ({{ $counts['en_marina'] }})
        </button>
        <button wire:click="$set('filterStatus', 'de_viaje')" class="whitespace-nowrap shrink-0 px-4 py-2 rounded-2xl text-xs font-bold uppercase tracking-wider transition-all {{ $filterStatus === 'de_viaje' ? 'btn-apple-navy' : 'glass-card text-blue-200/75 hover:text-white' }}">
            <i class="fas fa-compass mr-1.5 text-amber-400"></i>De Viaje ({{ $counts['de_viaje'] }})
        </button>
        <button wire:click="$set('filterStatus', 'unassigned')" class="whitespace-nowrap shrink-0 px-4 py-2 rounded-2xl text-xs font-bold uppercase tracking-wider transition-all {{ $filterStatus === 'unassigned' ? 'btn-apple-navy' : 'glass-card text-blue-200/75 hover:text-white' }}">
            <i class="fas fa-circle-question mr-1.5 text-blue-300"></i>Sin Puesto ({{ $counts['unassigned'] }})
        </button>
        <button wire:click="$set('filterStatus', 'de_baja')" class="whitespace-nowrap shrink-0 px-4 py-2 rounded-2xl text-xs font-bold uppercase tracking-wider transition-all {{ $filterStatus === 'de_baja' ? 'btn-apple-crimson' : 'glass-card text-rose-300/75 hover:text-rose-200' }}">
            <i class="fas fa-box-archive mr-1.5"></i>Dadas de Baja ({{ $counts['de_baja'] }})
        </button>
    </div>

    @if (session()->has('boat_message'))
        <div class="mb-4 px-5 py-3 rounded-2xl glass-card border border-emerald-400/40 bg-emerald-500/15 text-emerald-200 text-xs font-bold flex items-center gap-2.5 animate-fade-in shrink-0">
            <i class="fas fa-circle-check text-emerald-400 text-sm shrink-0"></i>
            <span>{{ session('boat_message') }}</span>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- 1. VISTA MÓVIL: TARJETAS ADAPTABLES (Visible en < 768px)  -->
    <!-- ========================================================= -->
    <div class="md:hidden space-y-3 pb-8">
        @forelse ($boats as $boat)
            @php
                $expiringDocs = $boat->documents->filter(fn($d) => !$d->expires_at->isPast() && $d->expires_at->lte(now()->addDays(30)))->count();
                $expiredDocs = $boat->documents->filter(fn($d) => $d->expires_at->isPast())->count();
            @endphp
            <div class="glass-panel p-4 rounded-3xl space-y-3">
                <!-- Fila 1: Nombre, Puesto y Píldora de Estado -->
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-bold text-base text-white truncate">{{ $boat->name }}</p>
                            @if ($boat->slip)
                                <span class="inline-flex items-center justify-center whitespace-nowrap shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-white shadow-sm"
                                      style="background-color: {{ $boat->slip->zone->hex_code }}">
                                    #{{ $boat->slip->code }}
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center whitespace-nowrap shrink-0 text-[9px] font-bold uppercase text-blue-200/60 bg-white/5 px-2.5 py-0.5 rounded-full border border-white/10">
                                    Sin Puesto
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-blue-200/65 mt-0.5">{{ $boat->matricula }}</p>
                    </div>

                    <!-- Píldora de Estado Indeformable -->
                    <div class="shrink-0">
                        @if ($boat->trashed())
                            <span class="inline-flex items-center justify-center whitespace-nowrap px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-400/30">
                                De Baja
                            </span>
                        @elseif ($boat->navigation_status === 'de_viaje')
                            <span class="inline-flex items-center justify-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-400/30">
                                <i class="fas fa-location-arrow text-[9px]"></i>
                                <span>{{ $boat->activeSailing?->destination ?? 'En Zarpe' }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                <i class="fas fa-anchor text-[9px]"></i>
                                <span>En Marina</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Fila 2: Armador y Socio -->
                <div class="text-xs border-t border-white/10 pt-2.5">
                    <p class="text-[10px] uppercase tracking-wider text-blue-200/50 font-bold">Armador / Propietario</p>
                    <p class="font-semibold text-blue-50">{{ $boat->owner_name }}</p>
                    @if ($boat->has_member)
                        <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex items-center whitespace-nowrap shrink-0 px-2 py-0.5 rounded-md bg-blue-500/25 border border-blue-400/40 text-blue-200 font-bold text-[10px] uppercase tracking-wider">
                                <i class="fas fa-id-badge mr-1"></i>Acción: {{ $boat->member_action_number }}
                            </span>
                            <span class="text-[11px] font-medium text-blue-200/90">{{ $boat->member_name }}</span>
                        </div>
                    @endif
                </div>

                <!-- Fila 3: Medidas Rápidas -->
                <div class="grid grid-cols-3 gap-2 text-center bg-white/5 p-2.5 rounded-2xl border border-white/10 text-[11px]">
                    <div>
                        <span class="block text-[9px] uppercase text-blue-200/50 font-bold">Eslora</span>
                        <span class="font-bold text-white whitespace-nowrap">{{ $boat->eslora_ft ? number_format($boat->eslora_ft, 2) : 'N/D' }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase text-blue-200/50 font-bold">Arqueo</span>
                        <span class="font-bold text-white whitespace-nowrap">{{ $boat->arqueo_bruto ? number_format($boat->arqueo_bruto, 2) . ' AB' : 'N/D' }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase text-blue-200/50 font-bold">Tripulación</span>
                        <span class="font-bold text-white whitespace-nowrap">{{ $boat->crew_capacity ? $boat->crew_capacity . ' pers.' : 'N/D' }}</span>
                    </div>
                </div>

                <!-- Fila 4: Documentos y Botones de Acción -->
                <div class="flex items-center justify-between pt-1 gap-2">
                    <div>
                        @if ($expiredDocs > 0)
                            <button wire:click="openDetailsModal({{ $boat->id }})" class="inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-[#C8102E] text-white shadow-sm">
                                <i class="fas fa-triangle-exclamation"></i>
                                <span>{{ $expiredDocs }} Vencido(s)</span>
                            </button>
                        @elseif ($expiringDocs > 0)
                            <button wire:click="openDetailsModal({{ $boat->id }})" class="inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-500/25 text-amber-300 border border-amber-400/40">
                                <i class="fas fa-clock"></i>
                                <span>{{ $expiringDocs }} Por Vencer</span>
                            </button>
                        @elseif ($boat->documents->count() > 0)
                            <button wire:click="openDetailsModal({{ $boat->id }})" class="inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/15 text-emerald-300 border border-emerald-400/30">
                                <i class="fas fa-file-shield"></i>
                                <span>{{ $boat->documents->count() }} Vigente(s)</span>
                            </button>
                        @else
                            <button wire:click="openDetailsModal({{ $boat->id }})" class="text-[11px] font-semibold text-blue-200/60 hover:text-white underline whitespace-nowrap">
                                + Registrar Docs
                            </button>
                        @endif
                    </div>

                    <div class="inline-flex items-center gap-1.5 shrink-0">
                        <button wire:click="openDetailsModal({{ $boat->id }})" class="w-8 h-8 rounded-xl glass-card text-blue-200 flex items-center justify-center">
                            <i class="fas fa-file-contract text-xs"></i>
                        </button>
                        <button wire:click="openEditModal({{ $boat->id }})" class="w-8 h-8 rounded-xl glass-card text-blue-200 flex items-center justify-center">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        @if (!$boat->trashed())
                            <button wire:click="openDeactivateModal({{ $boat->id }})" class="w-8 h-8 rounded-xl bg-rose-500/15 text-rose-300 border border-rose-400/30 flex items-center justify-center">
                                <i class="fas fa-ban text-xs"></i>
                            </button>
                        @else
                            <button wire:click="reactivateBoat({{ $boat->id }})" class="px-2.5 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 text-[10px] font-bold uppercase whitespace-nowrap">
                                Reactivar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-panel p-8 rounded-3xl text-center text-blue-200/60 text-xs">
                No se encontraron embarcaciones con los filtros seleccionados.
            </div>
        @endforelse
    </div>

    <!-- ========================================================= -->
    <!-- 2. VISTA ESCRITORIO / TABLET CON PROTECCIÓN DE ZOOM       -->
    <!-- ========================================================= -->
    <div class="hidden md:flex flex-1 min-h-0 glass-panel rounded-[2rem] overflow-hidden flex-col">
        <div class="flex-1 overflow-auto custom-scrollbar">
            <table class="w-full min-w-[1020px] text-left border-collapse">
                <thead>
                <tr class="border-b border-white/10 text-[10px] font-bold uppercase tracking-[0.16em] text-blue-200/60 bg-[#0A192F]/80 sticky top-0 backdrop-blur-md z-10 whitespace-nowrap">
                    <th class="py-4 px-5">Embarcación / Matrícula</th>
                    <th class="py-4 px-5">Armador & Socio del Club</th>
                    <th class="py-4 px-4 text-center">Puesto</th>
                    <th class="py-4 px-4 text-center">Eslora / Arqueo Bruto</th>
                    <th class="py-4 px-4 text-center">Tripulación</th>
                    <th class="py-4 px-4 text-center">Estado</th>
                    <th class="py-4 px-4 text-center">Licencia & Radio</th>
                    <th class="py-4 px-5 text-right">Acciones</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-white/10 text-xs">
                @forelse ($boats as $boat)
                    @php
                        $expiringDocs = $boat->documents->filter(fn($d) => !$d->expires_at->isPast() && $d->expires_at->lte(now()->addDays(30)))->count();
                        $expiredDocs = $boat->documents->filter(fn($d) => $d->expires_at->isPast())->count();
                    @endphp
                    <tr class="hover:bg-white/5 transition-colors">
                        <!-- Embarcación -->
                        <td class="py-3.5 px-5 whitespace-nowrap">
                            <p class="font-bold text-sm text-white">{{ $boat->name }}</p>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-200/65">{{ $boat->matricula }}</span>
                        </td>

                        <!-- Armador & Socio -->
                        <td class="py-3.5 px-5 max-w-xs">
                            <p class="font-semibold text-blue-50 truncate" title="{{ $boat->owner_name }}">{{ $boat->owner_name }}</p>
                            @if ($boat->has_member)
                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                        <span class="inline-flex items-center whitespace-nowrap shrink-0 px-2 py-0.5 rounded-md bg-blue-500/25 border border-blue-400/40 text-blue-200 font-bold text-[10px] uppercase tracking-wider">
                                            <i class="fas fa-id-badge mr-1"></i>Acción: {{ $boat->member_action_number }}
                                        </span>
                                    <span class="text-[11px] font-medium text-blue-200/90 truncate">{{ $boat->member_name }}</span>
                                </div>
                            @else
                                <span class="text-[10px] text-blue-200/40 uppercase tracking-wider whitespace-nowrap">Sin socio asignado</span>
                            @endif

                            @if ($boat->trashed() && $boat->deactivated_reason)
                                <p class="text-[10px] text-rose-300 mt-1 italic truncate">Motivo baja: {{ $boat->deactivated_reason }}</p>
                            @endif
                        </td>

                        <!-- Puesto -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if ($boat->slip)
                                <span class="inline-flex items-center justify-center whitespace-nowrap shrink-0 px-3 py-1 rounded-full text-[11px] font-bold text-white shadow-sm"
                                      style="background-color: {{ $boat->slip->zone->hex_code }}">
                                        #{{ $boat->slip->code }}
                                    </span>
                            @else
                                <span class="inline-flex items-center justify-center whitespace-nowrap shrink-0 text-[10px] font-bold uppercase text-blue-200/50 bg-white/5 px-3 py-1 rounded-full border border-white/10">
                                        Sin Puesto
                                    </span>
                            @endif
                        </td>

                        <!-- Eslora / Arqueo Bruto -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="font-bold text-white" title="Eslora">{{ $boat->eslora_ft ? number_format($boat->eslora_ft, 2) : 'N/D' }}</span>
                            <span class="text-blue-200/40 mx-1">/</span>
                            <span class="text-blue-200/85 font-semibold" title="Arqueo Bruto">{{ $boat->arqueo_bruto ? number_format($boat->arqueo_bruto, 2) . ' AB' : 'N/D' }}</span>
                        </td>

                        <!-- Tripulación -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if ($boat->crew_capacity)
                                <span class="inline-flex items-center justify-center gap-1 whitespace-nowrap shrink-0 px-2.5 py-1 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-[11px]">
                                        <i class="fas fa-users text-blue-300 text-[10px]"></i>
                                        <span>{{ $boat->crew_capacity }} pers.</span>
                                    </span>
                            @else
                                <span class="text-blue-200/40">N/D</span>
                            @endif
                        </td>

                        <!-- Píldora de Estado (Indeformable al hacer Zoom) -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if ($boat->trashed())
                                <span class="inline-flex items-center justify-center whitespace-nowrap shrink-0 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-400/30">
                                        De Baja
                                    </span>
                            @elseif ($boat->navigation_status === 'de_viaje')
                                <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap shrink-0 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-400/30">
                                        <i class="fas fa-location-arrow text-[9px]"></i>
                                        <span>{{ $boat->activeSailing?->destination ?? 'En Zarpe' }}</span>
                                    </span>
                            @else
                                <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap shrink-0 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                        <i class="fas fa-anchor text-[9px]"></i>
                                        <span>En Marina</span>
                                    </span>
                            @endif
                        </td>

                        <!-- Píldora de Documentos (Indeformable al hacer Zoom) -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if ($expiredDocs > 0)
                                <button wire:click="openDetailsModal({{ $boat->id }})" class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap shrink-0 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-[#C8102E] text-white shadow-sm">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <span>{{ $expiredDocs }} Vencido(s)</span>
                                </button>
                            @elseif ($expiringDocs > 0)
                                <button wire:click="openDetailsModal({{ $boat->id }})" class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap shrink-0 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-500/25 text-amber-300 border border-amber-400/40">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $expiringDocs }} Por Vencer</span>
                                </button>
                            @elseif ($boat->documents->count() > 0)
                                <button wire:click="openDetailsModal({{ $boat->id }})" class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap shrink-0 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/15 text-emerald-300 border border-emerald-400/30">
                                    <i class="fas fa-file-shield"></i>
                                    <span>{{ $boat->documents->count() }} Vigente(s)</span>
                                </button>
                            @else
                                <button wire:click="openDetailsModal({{ $boat->id }})" class="text-[10px] font-semibold text-blue-200/50 hover:text-white underline whitespace-nowrap">
                                    Registrar
                                </button>
                            @endif
                        </td>

                        <!-- Acciones -->
                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <button wire:click="openDetailsModal({{ $boat->id }})" title="Ficha, Socio y Documentos" class="w-8 h-8 rounded-xl glass-card hover:bg-white/20 text-blue-200 hover:text-white flex items-center justify-center transition-all shrink-0">
                                    <i class="fas fa-file-contract text-xs"></i>
                                </button>
                                <button wire:click="openEditModal({{ $boat->id }})" title="Editar Embarcación" class="w-8 h-8 rounded-xl glass-card hover:bg-white/20 text-blue-200 hover:text-white flex items-center justify-center transition-all shrink-0">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                                @if (!$boat->trashed())
                                    <button wire:click="openDeactivateModal({{ $boat->id }})" title="Dar de Baja" class="w-8 h-8 rounded-xl bg-rose-500/15 hover:bg-[#C8102E] text-rose-300 hover:text-white border border-rose-400/30 flex items-center justify-center transition-all shrink-0">
                                        <i class="fas fa-ban text-xs"></i>
                                    </button>
                                @else
                                    <button wire:click="reactivateBoat({{ $boat->id }})" class="px-2.5 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/40 text-emerald-300 text-[10px] font-bold uppercase whitespace-nowrap shrink-0">
                                        Reactivar
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-blue-200/60">
                            <i class="fas fa-ship text-3xl mb-2 block opacity-40"></i>
                            No se encontraron embarcaciones con los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: CREAR / EDITAR EMBARCACIÓN + SOCIO + DOCUMENTOS -->
    @if ($showBoatModal)
        <div class="fixed inset-0 bg-[#020c1b]/80 backdrop-blur-md z-[70] flex items-center justify-center p-3 sm:p-4">
            <div class="glass-panel rounded-[2rem] p-5 sm:p-7 max-w-2xl w-full animate-fade-in max-h-[92vh] overflow-y-auto custom-scrollbar">
                <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-5 gap-2">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-white">
                            {{ $editingBoatId ? 'Modificar Ficha de Embarcación' : 'Registrar Nueva Embarcación' }}
                        </h3>
                        <p class="text-xs text-blue-200/70">Datos del buque, armador, socio del club y certificados INEA</p>
                    </div>
                    <button wire:click="$set('showBoatModal', false)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center shrink-0">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <form wire:submit="saveBoat" class="space-y-5">
                    <!-- SECCIÓN 1: IDENTIFICACIÓN Y ARMADOR -->
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-rose-400 mb-3">
                            <i class="fas fa-ship mr-1.5"></i> 1. Datos de Identificación y Armador
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">Nombre del Buque *</label>
                                <input wire:model="name" type="text" required placeholder="Ej: XTRA MILE" class="w-full py-2.5 px-4 glass-card rounded-2xl text-sm text-white outline-none focus:ring-2 focus:ring-blue-400/40">
                                @error('name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">N° de Matrícula *</label>
                                <input wire:model="matricula" type="text" required placeholder="Ej: AGSI-RE-0799" class="w-full py-2.5 px-4 glass-card rounded-2xl text-sm text-white outline-none focus:ring-2 focus:ring-blue-400/40">
                                @error('matricula') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">Propietario / Armador (Según Licencia) *</label>
                                <input wire:model="owner_name" type="text" required placeholder="Ej: REESTRUCTURACIONES.COM, C.A." class="w-full py-2.5 px-4 glass-card rounded-2xl text-sm text-white outline-none focus:ring-2 focus:ring-blue-400/40">
                                @error('owner_name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">Puesto Asignado</label>
                                <select wire:model="slip_id" class="w-full py-2.5 px-3.5 glass-card rounded-2xl text-sm text-white bg-[#0A192F] border border-white/20 outline-none">
                                    <option value="" class="bg-[#0A192F] text-white">-- Sin Puesto --</option>
                                    @foreach ($allSlips as $s)
                                        <option value="{{ $s->id }}" class="bg-[#0A192F] text-white">
                                            #{{ $s->code }} — {{ $s->zone->name }} ({{ $s->status === 'free' ? 'Libre' : 'Ocup.' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: AFILIACIÓN DE SOCIO DEL CLUB -->
                    <div class="pt-3 border-t border-white/10">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-amber-300">
                                    <i class="fas fa-id-badge mr-1.5"></i> 2. Afiliación de Socio CGYC
                                </p>
                                <p class="text-[11px] text-blue-200/70">¿La embarcación pertenece a un Socio con Acción en el Club?</p>
                            </div>

                            <div class="flex bg-[#061020]/70 p-1 rounded-2xl border border-white/15 w-max shrink-0">
                                <button
                                    type="button"
                                    wire:click="$set('has_member', false)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider whitespace-nowrap transition-all {{ !$has_member ? 'btn-apple-navy' : 'text-blue-200/60 hover:text-white' }}"
                                >
                                    No tiene Socio
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('has_member', true)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider whitespace-nowrap transition-all {{ $has_member ? 'btn-apple-crimson' : 'text-blue-200/60 hover:text-white' }}"
                                >
                                    Sí es Socio
                                </button>
                            </div>
                        </div>

                        @if ($has_member)
                            <div class="glass-card p-4 rounded-2xl space-y-3.5 border border-amber-400/30 animate-fade-in">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-amber-200 mb-1">N° de Acción *</label>
                                        <input wire:model="member_action_number" type="text" placeholder="Ej: P-040" class="w-full py-2.5 px-3.5 glass-card rounded-xl text-xs text-white placeholder-blue-200/40 outline-none focus:ring-2 focus:ring-amber-400/50">
                                        @error('member_action_number') <span class="text-[10px] text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-amber-200 mb-1">Nombre Completo del Socio *</label>
                                        <input wire:model="member_name" type="text" placeholder="Nombre y Apellido del Socio titular" class="w-full py-2.5 px-3.5 glass-card rounded-xl text-xs text-white placeholder-blue-200/40 outline-none focus:ring-2 focus:ring-amber-400/50">
                                        @error('member_name') <span class="text-[10px] text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-white/10">
                                    <p class="text-[9px] font-bold uppercase tracking-widest text-blue-200/60 mb-2">Datos Adicionales de Contacto (Opcionales)</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[9px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">N° de Cédula / Pasaporte</label>
                                            <input wire:model="member_dni" type="text" placeholder="Ej: V-12345678" class="w-full py-2 px-3 glass-card rounded-xl text-xs text-white outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">Teléfono</label>
                                            <input wire:model="member_phone" type="text" placeholder="Ej: +58 414-0000000" class="w-full py-2 px-3 glass-card rounded-xl text-xs text-white outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">Correo Electrónico</label>
                                            <input wire:model="member_email" type="email" placeholder="socio@correo.com" class="w-full py-2 px-3 glass-card rounded-xl text-xs text-white outline-none">
                                            @error('member_email') <span class="text-[10px] text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- SECCIÓN 3: DIMENSIONES Y CAPACIDAD -->
                    <div class="pt-3 border-t border-white/10">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-300 mb-3">
                            <i class="fas fa-ruler-combined mr-1.5"></i> 3. Dimensiones, Arqueo y Tripulación
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">Eslora</label>
                                <input wire:model="eslora_ft" type="number" step="0.01" placeholder="Ej: 10.17" class="w-full py-2.5 px-3.5 glass-card rounded-2xl text-sm text-white outline-none focus:ring-2 focus:ring-blue-400/40">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">Arqueo Bruto (AB)</label>
                                <input wire:model="arqueo_bruto" type="number" step="0.01" placeholder="Ej: 10.77" class="w-full py-2.5 px-3.5 glass-card rounded-2xl text-sm text-white outline-none focus:ring-2 focus:ring-blue-400/40">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">Cap. Tripulación</label>
                                <input wire:model="crew_capacity" type="number" placeholder="Ej: 10" class="w-full py-2.5 px-3.5 glass-card rounded-2xl text-sm text-white outline-none focus:ring-2 focus:ring-blue-400/40">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">Aforo Muelle (ft)</label>
                                <input wire:model="aforo_ft" type="number" step="0.01" placeholder="Opcional" class="w-full py-2.5 px-3.5 glass-card rounded-2xl text-sm text-white outline-none focus:ring-2 focus:ring-blue-400/40">
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: DOCUMENTOS (LICENCIA DE NAVEGACIÓN Y CERTIFICADO DE RADIO) -->
                    <div class="pt-3 border-t border-white/10 space-y-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-300">
                            <i class="fas fa-file-shield mr-1.5"></i> 4. Documentos y Fechas de Vencimiento
                        </p>

                        <!-- Sub-bloque: Licencia de Navegación -->
                        <div class="glass-card p-4 rounded-2xl space-y-3">
                            <span class="text-xs font-bold text-white flex items-center gap-2">
                                <i class="fas fa-id-card text-blue-300"></i> Licencia de Navegación
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[9px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">N° de Documento</label>
                                    <input wire:model="licencia_number" type="text" placeholder="INEA/CAGSI/2026/01-A-777" class="w-full py-2 px-3 glass-card rounded-xl text-xs text-white outline-none">
                                    @error('licencia_number') <span class="text-[10px] text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">Fecha Expedición</label>
                                    <input wire:model="licencia_issued_at" type="date" class="w-full py-2 px-3 glass-card rounded-xl text-xs text-white outline-none">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">Fecha Vencimiento</label>
                                    <input wire:model="licencia_expires_at" type="date" class="w-full py-2 px-3 glass-card rounded-xl text-xs text-white outline-none">
                                    @error('licencia_expires_at') <span class="text-[10px] text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sub-bloque: Certificado de Radio -->
                        <div class="glass-card p-4 rounded-2xl space-y-3">
                            <span class="text-xs font-bold text-white flex items-center gap-2">
                                <i class="fas fa-tower-broadcast text-amber-300"></i> Certificado Nacional de Seguridad Radiotelefónica (Radio)
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[9px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">N° de Documento</label>
                                    <input wire:model="radio_number" type="text" placeholder="INEA/CAGSI/2026/13-A-756" class="w-full py-2 px-3 glass-card rounded-xl text-xs text-white outline-none">
                                    @error('radio_number') <span class="text-[10px] text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">Fecha Expedición</label>
                                    <input wire:model="radio_issued_at" type="date" class="w-full py-2 px-3 glass-card rounded-xl text-xs text-white outline-none">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold uppercase tracking-wider text-blue-200/80 mb-1">Fecha Vencimiento</label>
                                    <input wire:model="radio_expires_at" type="date" class="w-full py-2 px-3 glass-card rounded-xl text-xs text-white outline-none">
                                    @error('radio_expires_at') <span class="text-[10px] text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showBoatModal', false)" class="px-5 py-2.5 rounded-xl glass-card text-xs font-bold text-blue-200">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-apple-crimson px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                            <i class="fas fa-floppy-disk mr-1.5"></i> Guardar Embarcación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 2: EXPEDIENTE DE DOCUMENTOS + DATOS DEL SOCIO -->
    @if ($showDetailsModal && $selectedBoat)
        <div class="fixed inset-0 bg-[#020c1b]/75 backdrop-blur-md z-[70] flex items-center justify-center p-3 sm:p-4">
            <div class="glass-panel rounded-[2rem] p-5 sm:p-7 max-w-3xl w-full animate-fade-in max-h-[92vh] overflow-y-auto custom-scrollbar space-y-6">

                <div class="flex items-start justify-between border-b border-white/10 pb-4 gap-2">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-200/60">Expediente Oficial de Embarcación</span>
                        <h3 class="text-xl sm:text-2xl font-bold text-white mt-0.5">{{ $selectedBoat->name }}</h3>
                        <p class="text-xs text-blue-200/80 mt-1">
                            Matrícula: <strong class="text-white">{{ $selectedBoat->matricula }}</strong> ·
                            Armador: <strong class="text-white">{{ $selectedBoat->owner_name }}</strong>
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2.5">
                            <span class="whitespace-nowrap text-[10px] font-bold bg-white/10 px-2.5 py-1 rounded-lg text-blue-100">
                                Eslora: {{ $selectedBoat->eslora_ft ?? 'N/D' }}
                            </span>
                            <span class="whitespace-nowrap text-[10px] font-bold bg-white/10 px-2.5 py-1 rounded-lg text-blue-100">
                                Arqueo Bruto: {{ $selectedBoat->arqueo_bruto ? $selectedBoat->arqueo_bruto . ' AB' : 'N/D' }}
                            </span>
                            <span class="whitespace-nowrap text-[10px] font-bold bg-white/10 px-2.5 py-1 rounded-lg text-blue-100">
                                Capacidad: {{ $selectedBoat->crew_capacity ? $selectedBoat->crew_capacity . ' personas' : 'N/D' }}
                            </span>
                        </div>
                    </div>
                    <button wire:click="$set('showDetailsModal', false)" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center shrink-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                @if ($selectedBoat->has_member)
                    <div class="glass-card p-4 rounded-2xl border border-amber-400/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="whitespace-nowrap px-2.5 py-0.5 rounded-lg bg-amber-500/20 border border-amber-400/40 text-amber-300 font-bold text-[10px] uppercase tracking-wider">
                                    Acción N° {{ $selectedBoat->member_action_number }}
                                </span>
                                <span class="text-xs font-bold text-white">{{ $selectedBoat->member_name }}</span>
                            </div>
                            <div class="flex flex-wrap gap-4 mt-2 text-[11px] text-blue-200/80">
                                @if ($selectedBoat->member_dni)
                                    <span class="whitespace-nowrap"><i class="fas fa-id-card mr-1 text-blue-300"></i>C.I./ID: <strong class="text-white">{{ $selectedBoat->member_dni }}</strong></span>
                                @endif
                                @if ($selectedBoat->member_phone)
                                    <span class="whitespace-nowrap"><i class="fas fa-phone mr-1 text-blue-300"></i>Tel: <strong class="text-white">{{ $selectedBoat->member_phone }}</strong></span>
                                @endif
                                @if ($selectedBoat->member_email)
                                    <span class="whitespace-nowrap"><i class="fas fa-envelope mr-1 text-blue-300"></i>Correo: <strong class="text-white">{{ $selectedBoat->member_email }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if (session()->has('doc_message'))
                    <div class="px-4 py-2.5 rounded-xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-200 text-xs font-bold">
                        <i class="fas fa-check-circle mr-1.5"></i> {{ session('doc_message') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-blue-200 flex items-center gap-2">
                            <i class="fas fa-file-shield text-rose-400"></i> Certificados Registrados
                        </h4>

                        @forelse ($selectedBoat->documents as $doc)
                            @php
                                $daysLeft = (int) now()->startOfDay()->diffInDays($doc->expires_at->startOfDay(), false);
                                $isExpired = $daysLeft < 0;
                                $isSoon = !$isExpired && $daysLeft <= 30;
                            @endphp
                            <div class="glass-card p-4 rounded-2xl border {{ $isExpired ? 'border-[#C8102E]' : ($isSoon ? 'border-amber-400/50' : 'border-white/15') }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="font-bold text-xs text-white">{{ $doc->document_type }}</p>
                                        <p class="text-[11px] font-semibold text-blue-200 mt-0.5">Certificado N°: {{ $doc->document_number }}</p>
                                    </div>
                                    <button wire:click="deleteDocument({{ $doc->id }})" class="text-rose-300/70 hover:text-rose-400 text-xs shrink-0" title="Eliminar certificado">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </div>

                                <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between gap-2 text-[11px]">
                                    <div class="space-y-0.5">
                                        @if ($doc->issued_at)
                                            <p class="text-blue-200/70">Expedición: <span class="text-white font-semibold">{{ $doc->issued_at->format('d/m/Y') }}</span></p>
                                        @endif
                                        <p class="text-blue-200/70">Vencimiento: <strong class="text-white">{{ $doc->expires_at->format('d/m/Y') }}</strong></p>
                                    </div>

                                    @if ($isExpired)
                                        <span class="whitespace-nowrap shrink-0 px-2.5 py-1 rounded-lg bg-[#C8102E] text-white text-[9px] font-bold uppercase">
                                            Vencido ({{ abs($daysLeft) }}d)
                                        </span>
                                    @elseif ($isSoon)
                                        <span class="whitespace-nowrap shrink-0 px-2.5 py-1 rounded-lg bg-amber-500/25 text-amber-300 border border-amber-400/40 text-[9px] font-bold uppercase">
                                            Vence en {{ $daysLeft }} días
                                        </span>
                                    @else
                                        <span class="whitespace-nowrap shrink-0 px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[9px] font-bold uppercase">
                                            Vigente ({{ $daysLeft }}d)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="glass-card p-6 rounded-2xl text-center text-xs text-blue-200/60">
                                Sin certificados cargados para esta embarcación.
                            </div>
                        @endforelse
                    </div>

                    <div class="glass-card p-5 rounded-3xl space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-white">
                            <i class="fas fa-plus-circle mr-1.5 text-emerald-400"></i> Registrar / Renovar Certificado
                        </h4>

                        <form wire:submit="addDocument" class="space-y-3.5">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-blue-200 mb-1">Documento *</label>
                                <select wire:model="document_type" class="w-full py-2.5 px-3.5 glass-card rounded-xl text-xs text-white bg-[#0A192F] border border-white/20 outline-none">
                                    <option value="Licencia de Navegación" class="bg-[#0A192F]">Licencia de Navegación</option>
                                    <option value="Certificado de Radio" class="bg-[#0A192F]">Certificado de Radio (Seguridad Radiotelefónica)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-blue-200 mb-1">Certificado N° *</label>
                                <input wire:model="document_number" type="text" required placeholder="Ej: INEA/CAGSI/2026/01-A-777" class="w-full py-2.5 px-3.5 glass-card rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-blue-400/40">
                                @error('document_number') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-blue-200 mb-1">Fecha Expedición</label>
                                    <input wire:model="issued_at" type="date" class="w-full py-2.5 px-3.5 glass-card rounded-xl text-xs text-white outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-blue-200 mb-1">Válido Hasta *</label>
                                    <input wire:model="expires_at" type="date" required class="w-full py-2.5 px-3.5 glass-card rounded-xl text-xs text-white outline-none">
                                </div>
                            </div>
                            @error('expires_at') <span class="text-xs text-rose-400 block">{{ $message }}</span> @enderror

                            <button type="submit" class="btn-apple-navy w-full py-3 rounded-xl text-xs font-bold uppercase tracking-wider">
                                Guardar Certificado
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: CONFIRMAR BAJA LÓGICA -->
    @if ($showDeactivateModal && $deactivatingBoat)
        <div class="fixed inset-0 bg-[#020c1b]/75 backdrop-blur-md z-[70] flex items-center justify-center p-4">
            <div class="glass-panel rounded-[2rem] p-7 max-w-md w-full animate-fade-in">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-[#C8102E]/25 border border-[#C8102E]/40 flex items-center justify-center text-rose-300 shrink-0">
                        <i class="fas fa-ban text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Dar de Baja Embarcación</h3>
                        <p class="text-xs text-blue-200/70">{{ $deactivatingBoat->name }} ({{ $deactivatingBoat->matricula }})</p>
                    </div>
                </div>

                <form wire:submit="confirmDeactivateBoat" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-blue-200 mb-1.5">
                            Motivo del Retiro / Baja *
                        </label>
                        <textarea
                            wire:model="deactivated_reason"
                            rows="3"
                            required
                            placeholder="Ej: Venta de la embarcación, retiro definitivo..."
                            class="w-full p-3.5 glass-card rounded-2xl text-xs text-white placeholder-blue-200/40 outline-none focus:ring-2 focus:ring-[#C8102E]/50"
                        ></textarea>
                        @error('deactivated_reason')
                        <span class="text-xs text-rose-400 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showDeactivateModal', false)" class="px-5 py-2.5 rounded-xl glass-card text-xs font-bold text-blue-200">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-apple-crimson px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider">
                            Confirmar Baja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
