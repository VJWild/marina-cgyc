<?php

namespace App\Livewire;

use App\Models\Boat;
use App\Models\Sailing;
use App\Models\Slip;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SlipsBoard extends Component
{
    public string $search = '';
    public string $statusFilter = 'all'; // all | free | en_marina | de_viaje
    public string $assignSearch = '';

    public ?int $selectedSlipId = null;
    public array $collapsedZones = [];

    // Modal 1: Confirmar transferencia al asignar barco que ya tiene otro puesto
    public bool $showMoveConfirmModal = false;
    public ?int $pendingBoatId = null;

    // Modal 2: Cambiar de puesto desde un muelle ocupado
    public bool $showRelocateModal = false;
    public ?int $relocatingBoatId = null;
    public ?int $targetSlipId = null;

    // Modal 3: Registrar Zarpe con Destino
    public bool $showSailingModal = false;
    public ?int $sailingBoatId = null;
    public string $destination = '';
    public string $expectedReturnAt = '';

    // Modal 4: Registrar Retorno de Viaje
    public bool $showReturnModal = false;
    public ?int $returningBoatId = null;
    public ?int $returnSlipId = null;

    public function toggleZone(int $zoneId): void
    {
        if (in_array($zoneId, $this->collapsedZones)) {
            $this->collapsedZones = array_diff($this->collapsedZones, [$zoneId]);
        } else {
            $this->collapsedZones[] = $zoneId;
        }
    }

    public function selectSlip(int $slipId): void
    {
        $this->selectedSlipId = $slipId;
        $this->assignSearch = '';
    }

    public function closeSidePanel(): void
    {
        $this->selectedSlipId = null;
    }

    // 1. ASIGNAR EMBARCACIÓN AL PUESTO SELECCIONADO
    public function initiateAssignBoat(int $boatId): void
    {
        if (!$this->selectedSlipId) {
            return;
        }

        $boat = Boat::findOrFail($boatId);

        if ($boat->slip_id && $boat->slip_id !== $this->selectedSlipId) {
            $this->pendingBoatId = $boat->id;
            $this->showMoveConfirmModal = true;
            return;
        }

        $this->executeAssignBoat($boat->id, $this->selectedSlipId);
    }

    public function confirmMoveBoat(): void
    {
        if ($this->pendingBoatId && $this->selectedSlipId) {
            $this->executeAssignBoat($this->pendingBoatId, $this->selectedSlipId);
        }
        $this->showMoveConfirmModal = false;
        $this->pendingBoatId = null;
    }

    public function executeAssignBoat(int $boatId, int $newSlipId): void
    {
        $boat = Boat::findOrFail($boatId);
        $oldSlip = $boat->slip;

        $boat->update([
            'slip_id' => $newSlipId,
            'navigation_status' => $boat->navigation_status === 'de_baja' ? 'en_marina' : $boat->navigation_status,
        ]);

        if ($oldSlip) {
            $oldSlip->syncStatus();
        }

        $newSlip = Slip::find($newSlipId);
        $newSlip?->syncStatus();

        $this->selectedSlipId = $newSlipId;
        session()->flash('status_message', "Embarcación {$boat->name} asignada al puesto #{$newSlip->code}.");
    }

    // 2. CAMBIAR DE PUESTO (MUDANZA DIRECTA)
    public function openRelocateModal(int $boatId): void
    {
        $boat = Boat::findOrFail($boatId);
        $this->relocatingBoatId = $boat->id;
        $this->targetSlipId = $boat->slip_id;
        $this->showRelocateModal = true;
    }

    public function executeRelocateBoat(): void
    {
        $this->validate([
            'targetSlipId' => 'required|exists:slips,id',
        ]);

        if ($this->relocatingBoatId && $this->targetSlipId) {
            $this->executeAssignBoat($this->relocatingBoatId, (int) $this->targetSlipId);
        }

        $this->showRelocateModal = false;
        $this->relocatingBoatId = null;
    }

    // 3. LIBERAR PUESTO
    public function releaseBoat(int $boatId): void
    {
        $boat = Boat::findOrFail($boatId);
        $oldSlip = $boat->slip;

        $boat->update(['slip_id' => null]);

        if ($oldSlip) {
            $oldSlip->syncStatus();
        }

        session()->flash('status_message', "Se liberó a {$boat->name} del puesto.");
    }

    // 4. DAR ZARPE CON DESTINO
    public function openSailingModal(int $boatId): void
    {
        $this->sailingBoatId = $boatId;
        $this->destination = '';
        $this->expectedReturnAt = '';
        $this->showSailingModal = true;
    }

    public function registerSailing(): void
    {
        $this->validate([
            'destination' => 'required|string|max:180',
            'expectedReturnAt' => 'nullable|date',
        ], [
            'destination.required' => 'Indique el destino de zarpe de la embarcación.',
        ]);

        $boat = Boat::findOrFail($this->sailingBoatId);

        Sailing::create([
            'boat_id' => $boat->id,
            'origin_slip_id' => $boat->slip_id,
            'destination' => strtoupper(trim($this->destination)),
            'departed_at' => now(),
            'expected_return_at' => $this->expectedReturnAt ?: null,
            'user_id' => Auth::id(),
        ]);

        $boat->update([
            'navigation_status' => 'de_viaje',
        ]);

        $this->showSailingModal = false;
        $this->sailingBoatId = null;
        session()->flash('status_message', "Zarpe autorizado para {$boat->name} con destino a {$this->destination}.");
    }

    // 5. REGISTRAR RETORNO DE VIAJE
    public function openReturnModal(int $boatId): void
    {
        $boat = Boat::findOrFail($boatId);
        $this->returningBoatId = $boat->id;
        $this->returnSlipId = $boat->slip_id ?? $this->selectedSlipId;
        $this->showReturnModal = true;
    }

    public function registerReturn(): void
    {
        $this->validate([
            'returnSlipId' => 'required|exists:slips,id',
        ]);

        $boat = Boat::findOrFail($this->returningBoatId);
        $oldSlip = $boat->slip;

        $activeSailing = $boat->activeSailing;
        if ($activeSailing) {
            $activeSailing->update([
                'returned_at' => now(),
                'return_slip_id' => $this->returnSlipId,
            ]);
        }

        $boat->update([
            'slip_id' => $this->returnSlipId,
            'navigation_status' => 'en_marina',
        ]);

        if ($oldSlip && $oldSlip->id !== (int) $this->returnSlipId) {
            $oldSlip->syncStatus();
        }

        $newSlip = Slip::find($this->returnSlipId);
        $newSlip?->syncStatus();

        $this->selectedSlipId = (int) $this->returnSlipId;
        $this->showReturnModal = false;
        $this->returningBoatId = null;

        session()->flash('status_message', "Retorno registrado: {$boat->name} amarrada en puesto #{$newSlip->code}.");
    }

    public function render()
    {
        $searchTerm = strtolower(trim($this->search));

        $zones = Zone::orderBy('display_order')
            ->with(['slips' => function ($q) {
                $q->orderBy('sort_order')
                    ->with(['boats' => function ($bq) {
                        $bq->where('navigation_status', '!=', 'de_baja')
                            ->with('activeSailing');
                    }]);
            }])
            ->get();

        // Filtrado dinámico de puestos por búsqueda y estado
        $filteredZones = $zones->map(function ($zone) use ($searchTerm) {
            $filteredSlips = $zone->slips->filter(function ($slip) use ($searchTerm) {
                $hasBoats = $slip->boats->isNotEmpty();
                $anySailing = $hasBoats && $slip->boats->contains('navigation_status', 'de_viaje');

                if ($this->statusFilter === 'free' && $hasBoats) return false;
                if ($this->statusFilter === 'en_marina' && (!$hasBoats || $anySailing)) return false;
                if ($this->statusFilter === 'de_viaje' && !$anySailing) return false;

                if ($searchTerm === '') return true;

                $matchCode = str_contains(strtolower($slip->code), $searchTerm);
                $matchBoat = $slip->boats->contains(function ($boat) use ($searchTerm) {
                    return str_contains(strtolower($boat->name), $searchTerm)
                        || str_contains(strtolower($boat->matricula), $searchTerm)
                        || str_contains(strtolower($boat->owner_name), $searchTerm);
                });

                return $matchCode || $matchBoat;
            });

            $zone->setRelation('slips', $filteredSlips);
            return $zone;
        })->filter(fn ($zone) => $zone->slips->isNotEmpty());

        $selectedSlip = $this->selectedSlipId
            ? Slip::with(['zone', 'boats.activeSailing'])->find($this->selectedSlipId)
            : null;

        $assignableBoats = Boat::where('navigation_status', '!=', 'de_baja')
            ->with('slip')
            ->when($this->assignSearch, function ($q) {
                $term = '%' . trim($this->assignSearch) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('matricula', 'like', $term)
                        ->orWhere('owner_name', 'like', $term);
                });
            })
            ->orderByRaw('CASE WHEN slip_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->limit(25)
            ->get();

        $allSlips = Slip::with('zone')->orderBy('sort_order')->get();
        $pendingBoat = $this->pendingBoatId ? Boat::with('slip')->find($this->pendingBoatId) : null;
        $sailingBoat = $this->sailingBoatId ? Boat::with('slip')->find($this->sailingBoatId) : null;
        $returningBoat = $this->returningBoatId ? Boat::with(['slip', 'activeSailing'])->find($this->returningBoatId) : null;
        $relocatingBoat = $this->relocatingBoatId ? Boat::with('slip')->find($this->relocatingBoatId) : null;

        return view('livewire.slips-board', compact(
            'filteredZones',
            'selectedSlip',
            'assignableBoats',
            'allSlips',
            'pendingBoat',
            'sailingBoat',
            'returningBoat',
            'relocatingBoat'
        ));
    }
}
