<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marina Caraballeda Golf & Yacht Club — Portal Público</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logocgyc.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-full flex flex-col md:flex-row overflow-hidden bg-navy-mesh text-white">

<!-- Backdrop para versión Móvil -->
<div id="panelBackdrop" class="fixed inset-0 bg-[#020c1b]/70 backdrop-blur-sm z-40 hidden md:hidden transition-opacity" onclick="closePanel()"></div>

<!-- Área Principal (Secciones / Mapa) -->
<div class="flex-1 flex flex-col relative overflow-hidden h-screen p-4 md:p-6">

    <!-- Cabecera Glassmorphism Navy -->
    <div class="glass-panel rounded-3xl px-6 py-4 flex flex-col lg:flex-row lg:items-center justify-between z-10 shrink-0 mb-6">
        <div class="flex items-center justify-between mb-4 lg:mb-0">
            <div class="flex items-center">
                <img src="{{ asset('img/logocgyc.png') }}" alt="Logo CGYC" class="h-20 md:h-24 w-auto object-contain mr-5 shrink-0 drop-shadow-[0_8px_16px_rgba(0,0,0,0.4)]">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl md:text-3xl font-bold text-white tracking-tight leading-none">Marina Caraballeda</h1>
                        <span class="hidden sm:inline-block text-[9px] uppercase tracking-widest font-bold bg-white/10 text-blue-200 border border-white/15 px-3 py-1 rounded-full">
                            Vista Invitado
                        </span>
                    </div>
                    <p class="text-xs text-blue-200/75 uppercase tracking-[0.24em] font-medium mt-1.5">Golf & Yacht Club</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5 w-full lg:w-auto">
            <!-- Buscador -->
            <div class="relative w-full lg:w-64">
                <i class="fas fa-search absolute left-4 top-3.5 text-blue-200/50 text-sm"></i>
                <input
                    type="text"
                    id="mainSearch"
                    placeholder="Buscar embarcación o puesto..."
                    class="w-full pl-11 pr-4 py-2.5 glass-card rounded-2xl text-sm text-white placeholder-blue-200/45 focus:ring-2 focus:ring-blue-400/40 focus:bg-white/15 outline-none transition-all"
                    onkeyup="renderDirectory()"
                >
            </div>

            <!-- Selector de Vista -->
            <div class="flex bg-[#061020]/60 backdrop-blur-md p-1.5 rounded-2xl border border-white/15 justify-center">
                <button id="btnViewDirectory" onclick="switchView('directory')" class="flex-1 sm:flex-initial px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl btn-apple-navy">
                    <i class="fas fa-th-large mr-1.5"></i>Puestos
                </button>
                <button id="btnViewMap" onclick="switchView('map')" class="flex-1 sm:flex-initial px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl text-blue-200/70 hover:text-white transition-all">
                    <i class="fas fa-map mr-1.5"></i>Mapa
                </button>
            </div>

            <!-- Botón de Acceso con Efecto de Alza Apple -->
            @auth
                <a href="{{ route('dashboard') }}" class="btn-apple-navy px-5 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-wider flex items-center justify-center">
                    <i class="fas fa-compass mr-2"></i>Panel Capitanía
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-apple-crimson px-5 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-wider flex items-center justify-center">
                    <i class="fas fa-lock mr-2"></i>Iniciar Sesión
                </a>
            @endauth
        </div>
    </div>

    <!-- Contenedor Directorio por Zonas -->
    <div id="viewDirectory" class="flex-1 overflow-y-auto custom-scrollbar pr-2 pb-10">
        <div id="directoryContainer" class="space-y-6"></div>
    </div>

    <!-- Contenedor Mapa -->
    <div id="viewMap" class="hidden flex-1 overflow-auto relative glass-panel rounded-3xl p-4">
        <div class="w-[1600px] h-[1100px] mx-auto bg-no-repeat bg-center rounded-2xl relative z-10"
             style="background-image: url('{{ asset('img/SECCIONES DEL MAPA.png') }}'); background-size: contain;">
        </div>
    </div>
</div>

<!-- Panel Lateral de Información (Censurado para Invitados) -->
<div id="sidePanel" class="fixed inset-y-0 right-0 z-50 w-[90%] max-w-sm md:w-[390px] md:max-w-none transform translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:relative p-4 md:p-6 md:pl-0 h-screen flex flex-col">
    <div class="glass-panel rounded-3xl flex-1 flex flex-col overflow-hidden relative">

        <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500 opacity-15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Cabecera del Panel -->
        <div class="p-6 md:p-8 pb-5 border-b border-white/10 shrink-0 relative z-10">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-[10px] uppercase tracking-[0.22em] text-blue-200/60 font-bold">Consulta de Puesto</h2>
                <button onclick="closePanel()" class="md:hidden w-8 h-8 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-end">
                    <span class="text-3xl font-light text-blue-300/50 mr-1 pb-1">#</span>
                    <span id="selectedSlipCode" class="text-5xl md:text-6xl font-bold tracking-tighter text-white">--</span>
                </div>
                <span id="selectedSlipSection" class="text-[10px] uppercase font-bold px-3.5 py-1.5 rounded-full shadow-md text-white bg-slate-600 tracking-wider">
                    Sin Selección
                </span>
            </div>

            <div class="mt-5 flex items-center glass-card w-max px-4 py-2 rounded-2xl">
                <div id="statusIndicator" class="w-2.5 h-2.5 rounded-full bg-slate-400 mr-3"></div>
                <span id="statusText" class="font-bold text-xs tracking-wider text-blue-100 uppercase">Esperando Selección</span>
            </div>
        </div>

        <!-- Cuerpo del Panel -->
        <div class="p-6 md:p-8 flex-1 overflow-y-auto custom-scrollbar relative z-10">
            <!-- Estado Inicial -->
            <div id="emptyState" class="h-full flex flex-col items-center justify-center text-blue-200/50">
                <div class="w-20 h-20 rounded-full glass-card flex items-center justify-center mb-5">
                    <i class="fas fa-anchor text-2xl text-blue-200/60"></i>
                </div>
                <p class="text-sm font-medium text-center text-blue-100/70">Selecciona cualquier muelle para<br>consultar la embarcación asignada.</p>
            </div>

            <!-- Vista de Puesto Seleccionado -->
            <div id="slipDetailsView" class="hidden animate-fade-in h-full flex flex-col justify-between">
                <div class="space-y-4">
                    <h3 class="text-blue-200/80 font-bold uppercase tracking-[0.16em] text-[10px] flex items-center">
                        <i class="fas fa-ship mr-2 text-blue-300/60"></i> Embarcación en Muelle
                    </h3>

                    <div id="boatsListContainer" class="space-y-3"></div>
                </div>

                <!-- Aviso de Privacidad Institucional -->
                <div class="glass-card p-4 rounded-2xl mt-6">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#C8102E]/25 border border-[#C8102E]/40 text-rose-300 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-shield-halved text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-white uppercase tracking-wider">Información Protegida</p>
                            <p class="text-xs text-blue-200/70 mt-1 leading-relaxed">
                                Datos de armador, matrícula, eslora y documentación son de acceso exclusivo para personal autorizado de Capitanía.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const zonesData = @json($zones);
    let currentSelectedSlipCode = null;
    let collapsedSections = new Set();

    function openPanel() {
        document.getElementById('sidePanel').classList.remove('translate-x-full');
        document.getElementById('panelBackdrop').classList.remove('hidden');
    }

    function closePanel() {
        document.getElementById('sidePanel').classList.add('translate-x-full');
        document.getElementById('panelBackdrop').classList.add('hidden');
    }

    function switchView(view) {
        const btnDir = document.getElementById('btnViewDirectory');
        const btnMap = document.getElementById('btnViewMap');
        const vDir = document.getElementById('viewDirectory');
        const vMap = document.getElementById('viewMap');

        if (view === 'directory') {
            btnDir.className = "flex-1 sm:flex-initial px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl btn-apple-navy";
            btnMap.className = "flex-1 sm:flex-initial px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl text-blue-200/70 hover:text-white transition-all";
            vDir.classList.remove('hidden');
            vMap.classList.add('hidden');
        } else {
            btnMap.className = "flex-1 sm:flex-initial px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl btn-apple-navy";
            btnDir.className = "flex-1 sm:flex-initial px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl text-blue-200/70 hover:text-white transition-all";
            vMap.classList.remove('hidden');
            vDir.classList.add('hidden');
        }
    }

    function toggleSection(zoneName) {
        if (collapsedSections.has(zoneName)) {
            collapsedSections.delete(zoneName);
        } else {
            collapsedSections.add(zoneName);
        }
        renderDirectory();
    }

    function renderDirectory() {
        const container = document.getElementById('directoryContainer');
        container.innerHTML = '';
        const searchText = document.getElementById('mainSearch').value.toLowerCase().trim();
        const isSearching = searchText.length > 0;

        zonesData.forEach(zone => {
            const filteredSlips = zone.slips.filter(slip => {
                const matchCode = slip.code.toLowerCase().includes(searchText);
                const matchBoat = slip.boats.some(b => b.name.toLowerCase().includes(searchText));
                return matchCode || matchBoat;
            });

            if (filteredSlips.length === 0) return;

            const isCollapsed = !isSearching && collapsedSections.has(zone.name);
            const gridDisplay = isCollapsed ? 'hidden' : 'grid';
            const chevronRotation = isCollapsed ? 'rotate-180' : '';

            const sectionDiv = document.createElement('div');
            sectionDiv.className = "glass-panel p-5 md:p-6 rounded-[2rem]";

            sectionDiv.innerHTML = `
                <div class="flex items-center pl-1 md:pl-2 cursor-pointer group select-none" onclick="toggleSection('${zone.name}')">
                    <div class="w-3.5 h-3.5 rounded-full mr-3 shadow-md transition-transform group-hover:scale-125" style="background-color: ${zone.hex_code}"></div>
                    <h2 class="text-sm md:text-base font-bold text-white uppercase tracking-widest">Zona ${zone.name}</h2>
                    <span class="ml-auto md:ml-4 text-[10px] font-bold text-blue-200 bg-white/10 px-3 py-1 rounded-full border border-white/15">${filteredSlips.length} ptos.</span>
                    <div class="ml-3 w-7 h-7 flex items-center justify-center rounded-full bg-white/10 group-hover:bg-white/20 transition-colors">
                        <i class="fas fa-chevron-up text-white text-[10px] transition-transform duration-300 ${chevronRotation}"></i>
                    </div>
                </div>
                <div class="${gridDisplay} grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5 md:gap-4 mt-5" id="grid-${zone.name}"></div>
            `;

            container.appendChild(sectionDiv);
            const gridDiv = sectionDiv.querySelector(`#grid-${zone.name}`);

            filteredSlips.forEach(slip => {
                const hasBoats = slip.boats && slip.boats.length > 0;
                const isShared = hasBoats && slip.boats.length > 1;
                const isSelected = currentSelectedSlipCode === slip.code;

                const card = document.createElement('div');
                card.className = `glass-card glass-card-hover p-3 md:p-3.5 rounded-3xl cursor-pointer flex flex-col justify-between items-center text-center min-h-[7rem] md:min-h-[7.5rem] relative overflow-hidden group ${
                    isSelected ? 'ring-2 ring-white bg-white/20 scale-[1.02]' : ''
                }`;

                if (hasBoats) {
                    const boatsHtml = slip.boats.map(b => {
                        const isSailing = b.navigation_status === 'de_viaje';
                        const dotColor = isSailing ? 'bg-amber-400' : 'bg-emerald-400';
                        return `
                            <div class="w-full px-2 py-1 rounded-xl bg-white/10 border border-white/10 flex items-center justify-between gap-1.5">
                                <span class="font-bold text-[11px] text-white truncate">${b.name}</span>
                                <span class="w-2 h-2 rounded-full shrink-0 ${dotColor}" title="${isSailing ? 'En Zarpe' : 'En Marina'}"></span>
                            </div>
                        `;
                    }).join('');

                    card.innerHTML = `
                        <div class="absolute top-0 w-full h-1.5" style="background-color: ${zone.hex_code}"></div>
                        <div class="w-full flex items-center justify-between px-1 pt-1 mb-1">
                            <span class="text-[10px] font-bold text-blue-200/70 tracking-widest">Nº ${slip.code}</span>
                            ${isShared ? `<span class="text-[8px] font-bold uppercase bg-blue-500/30 text-blue-200 px-1.5 py-0.5 rounded-md border border-blue-400/30">2 Barcos</span>` : ''}
                        </div>
                        <div class="w-full space-y-1 my-auto">
                            ${boatsHtml}
                        </div>
                    `;
                } else {
                    card.innerHTML = `
                        <div class="absolute top-0 w-full h-1.5 bg-white/15"></div>
                        <span class="text-[10px] font-bold text-blue-200/50 mt-2 tracking-widest">Nº ${slip.code}</span>
                        <span class="my-auto font-bold text-[9px] uppercase text-emerald-300 bg-emerald-500/15 px-3 py-1 rounded-full border border-emerald-400/30">
                            <i class="fas fa-check mr-1"></i>Libre
                        </span>
                    `;
                }

                card.onclick = () => selectSlip(slip, zone);
                gridDiv.appendChild(card);
            });
        });
    }

    function selectSlip(slip, zone) {
        currentSelectedSlipCode = slip.code;
        openPanel();

        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('slipDetailsView').classList.remove('hidden');
        document.getElementById('selectedSlipCode').textContent = slip.code;

        const sectionBadge = document.getElementById('selectedSlipSection');
        sectionBadge.textContent = `Zona ${zone.name}`;
        sectionBadge.style.backgroundColor = zone.hex_code;

        const statusInd = document.getElementById('statusIndicator');
        const statusTxt = document.getElementById('statusText');
        const boatsContainer = document.getElementById('boatsListContainer');
        boatsContainer.innerHTML = '';

        if (slip.boats && slip.boats.length > 0) {
            statusInd.className = 'w-2.5 h-2.5 rounded-full bg-emerald-400 mr-3 shadow-[0_0_10px_rgba(52,211,153,0.8)]';
            statusTxt.textContent = 'Puesto Ocupado';

            slip.boats.forEach(boat => {
                const isSailing = boat.navigation_status === 'de_viaje';
                const navBadge = isSailing
                    ? `<span class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30"><i class="fas fa-location-arrow mr-1.5"></i>De Viaje / En Zarpe</span>`
                    : `<span class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30"><i class="fas fa-anchor mr-1.5"></i>Amarrada en Marina</span>`;

                boatsContainer.innerHTML += `
                    <div class="glass-card p-5 rounded-3xl">
                        <p class="text-[10px] text-blue-200/60 uppercase font-bold tracking-widest mb-1">Nombre de Embarcación</p>
                        <p class="text-xl md:text-2xl font-bold text-white tracking-tight mb-3">${boat.name}</p>
                        ${navBadge}
                    </div>
                `;
            });
        } else {
            statusInd.className = 'w-2.5 h-2.5 rounded-full bg-slate-400 mr-3';
            statusTxt.textContent = 'Puesto Disponible';
            boatsContainer.innerHTML = `
                <div class="glass-card p-6 rounded-3xl text-center">
                    <p class="text-sm font-semibold text-blue-100/80">Este muelle se encuentra libre actualmente.</p>
                </div>
            `;
        }

        renderDirectory();
    }

    window.onload = () => renderDirectory();
</script>
</body>
</html>
