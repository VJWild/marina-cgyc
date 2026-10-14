<?php

namespace App\Livewire;

use App\Models\Boat;
use App\Models\BoatDocument;
use App\Models\Slip;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BoatsInventory extends Component
{
    public string $search = '';
    public string $filterStatus = 'all'; // all | en_marina | de_viaje | unassigned | de_baja

    // Modal Crear / Editar Embarcación (Datos Técnicos y Armador)
    public bool $showBoatModal = false;
    public ?int $editingBoatId = null;
    public string $name = '';
    public string $matricula = '';
    public string $owner_name = '';
    public ?string $eslora_ft = null;
    public ?string $arqueo_bruto = null;
    public ?int $crew_capacity = null;
    public ?string $aforo_ft = null;
    public ?int $slip_id = null;

    // Datos del Socio del Club
    public bool $has_member = false;
    public string $member_action_number = '';
    public string $member_name = '';
    public string $member_dni = '';
    public string $member_phone = '';
    public string $member_email = '';

    // Campos de Documentos integrados en el Modal de Embarcación
    public string $licencia_number = '';
    public ?string $licencia_issued_at = null;
    public ?string $licencia_expires_at = null;

    public string $radio_number = '';
    public ?string $radio_issued_at = null;
    public ?string $radio_expires_at = null;

    // Modal Ficha Detallada y Gestión Individual de Documentos
    public bool $showDetailsModal = false;
    public ?int $selectedBoatId = null;
    public string $document_type = 'Licencia de Navegación';
    public string $document_number = '';
    public ?string $issued_at = null;
    public string $expires_at = '';

    // Modal Dar de Baja Lógica
    public bool $showDeactivateModal = false;
    public ?int $deactivatingBoatId = null;
    public string $deactivated_reason = '';

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->editingBoatId = null;
        $this->name = '';
        $this->matricula = '';
        $this->owner_name = '';
        $this->eslora_ft = null;
        $this->arqueo_bruto = null;
        $this->crew_capacity = null;
        $this->aforo_ft = null;
        $this->slip_id = null;

        $this->has_member = false;
        $this->member_action_number = '';
        $this->member_name = '';
        $this->member_dni = '';
        $this->member_phone = '';
        $this->member_email = '';

        $this->licencia_number = '';
        $this->licencia_issued_at = null;
        $this->licencia_expires_at = null;

        $this->radio_number = '';
        $this->radio_issued_at = null;
        $this->radio_expires_at = null;

        $this->showBoatModal = true;
    }

    public function openEditModal(int $boatId): void
    {
        $this->resetValidation();
        $boat = Boat::withTrashed()->with('documents')->findOrFail($boatId);

        $this->editingBoatId = $boat->id;
        $this->name = $boat->name;
        $this->matricula = $boat->matricula;
        $this->owner_name = $boat->owner_name;
        $this->eslora_ft = $boat->eslora_ft !== null ? (string) $boat->eslora_ft : null;
        $this->arqueo_bruto = $boat->arqueo_bruto !== null ? (string) $boat->arqueo_bruto : null;
        $this->crew_capacity = $boat->crew_capacity;
        $this->aforo_ft = $boat->aforo_ft !== null ? (string) $boat->aforo_ft : null;
        $this->slip_id = $boat->slip_id;

        $this->has_member = (bool) $boat->has_member;
        $this->member_action_number = $boat->member_action_number ?? '';
        $this->member_name = $boat->member_name ?? '';
        $this->member_dni = $boat->member_dni ?? '';
        $this->member_phone = $boat->member_phone ?? '';
        $this->member_email = $boat->member_email ?? '';

        $licencia = $boat->documents->firstWhere('document_type', 'Licencia de Navegación');
        $this->licencia_number = $licencia?->document_number ?? '';
        $this->licencia_issued_at = $licencia?->issued_at?->format('Y-m-d');
        $this->licencia_expires_at = $licencia?->expires_at?->format('Y-m-d');

        $radio = $boat->documents->firstWhere('document_type', 'Certificado de Radio');
        $this->radio_number = $radio?->document_number ?? '';
        $this->radio_issued_at = $radio?->issued_at?->format('Y-m-d');
        $this->radio_expires_at = $radio?->expires_at?->format('Y-m-d');

        $this->showBoatModal = true;
    }

    public function saveBoat(): void
    {
        $this->validate([
            'name' => 'required|string|max:120',
            'matricula' => 'required|string|max:60',
            'owner_name' => 'required|string|max:180',
            'eslora_ft' => 'nullable|numeric|min:0|max:999.99',
            'arqueo_bruto' => 'nullable|numeric|min:0|max:999.99',
            'crew_capacity' => 'nullable|integer|min:1|max:500',
            'aforo_ft' => 'nullable|numeric|min:0|max:999.99',
            'slip_id' => ['nullable', Rule::exists('slips', 'id')],

            // Validación condicional si pertenece a un Socio
            'has_member' => 'boolean',
            'member_action_number' => 'nullable|required_if:has_member,true|string|max:30',
            'member_name' => 'nullable|required_if:has_member,true|string|max:180',
            'member_dni' => 'nullable|string|max:30',
            'member_phone' => 'nullable|string|max:40',
            'member_email' => 'nullable|email|max:150',

            // Validación condicional de Certificados
            'licencia_number' => 'nullable|string|max:80|required_with:licencia_expires_at',
            'licencia_issued_at' => 'nullable|date',
            'licencia_expires_at' => 'nullable|date|required_with:licencia_number',

            'radio_number' => 'nullable|string|max:80|required_with:radio_expires_at',
            'radio_issued_at' => 'nullable|date',
            'radio_expires_at' => 'nullable|date|required_with:radio_number',
        ], [
            'name.required' => 'El nombre de la embarcación es obligatorio.',
            'matricula.required' => 'La matrícula es obligatoria.',
            'owner_name.required' => 'El nombre del armador/propietario es obligatorio.',
            'member_action_number.required_if' => 'Indique el N° de Acción del socio (Ej: P-040).',
            'member_name.required_if' => 'Indique el nombre completo del socio.',
            'member_email.email' => 'Ingrese un formato de correo válido.',
            'licencia_number.required_with' => 'Indique el N° de Licencia de Navegación.',
            'licencia_expires_at.required_with' => 'Indique la fecha de vencimiento de la Licencia.',
            'radio_number.required_with' => 'Indique el N° del Certificado de Radio.',
            'radio_expires_at.required_with' => 'Indique la fecha de vencimiento del Certificado de Radio.',
        ]);

        $newSlipId = $this->slip_id ?: null;

        $data = [
            'name' => strtoupper(trim($this->name)),
            'matricula' => strtoupper(trim($this->matricula)),
            'owner_name' => strtoupper(trim($this->owner_name)),
            'has_member' => $this->has_member,
            'member_action_number' => $this->has_member ? strtoupper(trim($this->member_action_number)) : null,
            'member_name' => $this->has_member ? strtoupper(trim($this->member_name)) : null,
            'member_dni' => ($this->has_member && trim($this->member_dni) !== '') ? strtoupper(trim($this->member_dni)) : null,
            'member_phone' => ($this->has_member && trim($this->member_phone) !== '') ? trim($this->member_phone) : null,
            'member_email' => ($this->has_member && trim($this->member_email) !== '') ? strtolower(trim($this->member_email)) : null,
            'eslora_ft' => $this->eslora_ft !== '' && $this->eslora_ft !== null ? $this->eslora_ft : null,
            'arqueo_bruto' => $this->arqueo_bruto !== '' && $this->arqueo_bruto !== null ? $this->arqueo_bruto : null,
            'crew_capacity' => $this->crew_capacity ?: null,
            'aforo_ft' => $this->aforo_ft !== '' && $this->aforo_ft !== null ? $this->aforo_ft : null,
            'slip_id' => $newSlipId,
        ];

        if ($this->editingBoatId) {
            $boat = Boat::withTrashed()->findOrFail($this->editingBoatId);
            $oldSlip = $boat->slip;
            $boat->update($data);

            if ($oldSlip && $oldSlip->id !== $newSlipId) {
                $oldSlip->syncStatus();
            }
            $actionText = "Ficha de {$boat->name} actualizada correctamente.";
        } else {
            $data['navigation_status'] = 'en_marina';
            $boat = Boat::create($data);
            $actionText = "Embarcación {$boat->name} registrada exitosamente en la base de datos.";
        }

        if (trim($this->licencia_number) !== '' && $this->licencia_expires_at) {
            BoatDocument::updateOrCreate(
                ['boat_id' => $boat->id, 'document_type' => 'Licencia de Navegación'],
                [
                    'document_number' => strtoupper(trim($this->licencia_number)),
                    'issued_at' => $this->licencia_issued_at ?: null,
                    'expires_at' => $this->licencia_expires_at,
                ]
            );
        }

        if (trim($this->radio_number) !== '' && $this->radio_expires_at) {
            BoatDocument::updateOrCreate(
                ['boat_id' => $boat->id, 'document_type' => 'Certificado de Radio'],
                [
                    'document_number' => strtoupper(trim($this->radio_number)),
                    'issued_at' => $this->radio_issued_at ?: null,
                    'expires_at' => $this->radio_expires_at,
                ]
            );
        }

        if ($newSlipId) {
            Slip::find($newSlipId)?->syncStatus();
        }

        $this->showBoatModal = false;
        session()->flash('boat_message', $actionText);
    }

    public function openDetailsModal(int $boatId): void
    {
        $this->resetValidation();
        $this->selectedBoatId = $boatId;
        $this->resetDocumentForm();
        $this->showDetailsModal = true;
    }

    public function resetDocumentForm(): void
    {
        $this->document_type = 'Licencia de Navegación';
        $this->document_number = '';
        $this->issued_at = null;
        $this->expires_at = '';
    }

    public function addDocument(): void
    {
        $this->validate([
            'document_type' => 'required|string|max:80',
            'document_number' => 'required|string|max:80',
            'issued_at' => 'nullable|date',
            'expires_at' => 'required|date',
        ], [
            'document_number.required' => 'Ingrese el número de certificado/documento.',
            'expires_at.required' => 'Indique la fecha de vencimiento.',
        ]);

        BoatDocument::updateOrCreate(
            [
                'boat_id' => $this->selectedBoatId,
                'document_type' => $this->document_type,
            ],
            [
                'document_number' => strtoupper(trim($this->document_number)),
                'issued_at' => $this->issued_at ?: null,
                'expires_at' => $this->expires_at,
            ]
        );

        $this->resetDocumentForm();
        session()->flash('doc_message', 'Documento guardado correctamente en el expediente.');
    }

    public function deleteDocument(int $docId): void
    {
        BoatDocument::where('boat_id', $this->selectedBoatId)->where('id', $docId)->delete();
        session()->flash('doc_message', 'Documento eliminado del expediente.');
    }

    public function openDeactivateModal(int $boatId): void
    {
        $this->resetValidation();
        $this->deactivatingBoatId = $boatId;
        $this->deactivated_reason = '';
        $this->showDeactivateModal = true;
    }

    public function confirmDeactivateBoat(): void
    {
        $this->validate([
            'deactivated_reason' => 'required|string|min:5|max:500',
        ], [
            'deactivated_reason.required' => 'Especifique el motivo de la baja de la embarcación.',
        ]);

        $boat = Boat::findOrFail($this->deactivatingBoatId);
        $oldSlip = $boat->slip;

        $boat->update([
            'slip_id' => null,
            'navigation_status' => 'de_baja',
            'deactivated_reason' => trim($this->deactivated_reason),
        ]);

        $boat->delete();

        if ($oldSlip) {
            $oldSlip->syncStatus();
        }

        $this->showDeactivateModal = false;
        $this->deactivatingBoatId = null;

        session()->flash('boat_message', "La embarcación {$boat->name} fue dada de baja y su puesto quedó libre.");
    }

    public function reactivateBoat(int $boatId): void
    {
        $boat = Boat::withTrashed()->findOrFail($boatId);
        $boat->restore();
        $boat->update([
            'navigation_status' => 'en_marina',
            'deactivated_reason' => null,
        ]);

        session()->flash('boat_message', "La embarcación {$boat->name} fue reactivada en el inventario.");
    }

    public function render()
    {
        $query = Boat::query()->with(['slip.zone', 'documents', 'activeSailing']);

        if ($this->filterStatus === 'de_baja') {
            $query->onlyTrashed();
        } elseif ($this->filterStatus === 'en_marina') {
            $query->where('navigation_status', 'en_marina')->whereNotNull('slip_id');
        } elseif ($this->filterStatus === 'de_viaje') {
            $query->where('navigation_status', 'de_viaje');
        } elseif ($this->filterStatus === 'unassigned') {
            $query->whereNull('slip_id');
        }

        if (trim($this->search) !== '') {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($sub) use ($term) {
                $sub->where('name', 'like', $term)
                    ->orWhere('matricula', 'like', $term)
                    ->orWhere('owner_name', 'like', $term)
                    ->orWhere('member_action_number', 'like', $term)
                    ->orWhere('member_name', 'like', $term)
                    ->orWhere('member_dni', 'like', $term);
            });
        }

        $boats = $query->orderBy('name')->get();
        $allSlips = Slip::with('zone')->orderBy('sort_order')->get();

        $selectedBoat = $this->selectedBoatId
            ? Boat::withTrashed()->with(['slip.zone', 'documents' => fn($q) => $q->orderBy('expires_at'), 'sailings'])->find($this->selectedBoatId)
            : null;

        $deactivatingBoat = $this->deactivatingBoatId
            ? Boat::with('slip')->find($this->deactivatingBoatId)
            : null;

        $counts = [
            'all' => Boat::count(),
            'en_marina' => Boat::where('navigation_status', 'en_marina')->whereNotNull('slip_id')->count(),
            'de_viaje' => Boat::where('navigation_status', 'de_viaje')->count(),
            'unassigned' => Boat::whereNull('slip_id')->count(),
            'de_baja' => Boat::onlyTrashed()->count(),
        ];

        return view('livewire.boats-inventory', compact(
            'boats',
            'allSlips',
            'selectedBoat',
            'deactivatingBoat',
            'counts'
        ));
    }
}
