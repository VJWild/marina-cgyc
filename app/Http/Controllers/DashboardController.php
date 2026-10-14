<?php

namespace App\Http\Controllers;

use App\Models\Boat;
use App\Models\BoatDocument;
use App\Models\Sailing;
use App\Models\Slip;
use App\Models\Zone;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Métricas de Embarcaciones (KPIs)
        $totalBoats = Boat::count();
        $boatsInMarina = Boat::where('navigation_status', 'en_marina')->count();
        $boatsSailing = Boat::where('navigation_status', 'de_viaje')->count();

        // 2. Métricas de Muelles / Puestos
        $totalSlips = Slip::count();
        $occupiedSlips = Slip::whereHas('boats', function ($q) {
            $q->where('navigation_status', '!=', 'de_baja');
        })->count();
        $freeSlips = $totalSlips - $occupiedSlips;

        // 3. Alertas de Documentos (Vencidos o por vencer en los próximos 30 días)
        $expiringDocuments = BoatDocument::with(['boat.slip.zone'])
            ->whereHas('boat', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->where('expires_at', '<=', now()->addDays(30))
            ->orderBy('expires_at', 'asc')
            ->get();

        // 4. Embarcaciones actualmente por fuera (En Zarpe Activo)
        $activeSailings = Sailing::with(['boat.slip.zone', 'originSlip'])
            ->whereNull('returned_at')
            ->whereHas('boat', function ($q) {
                $q->where('navigation_status', 'de_viaje');
            })
            ->latest('departed_at')
            ->get();

        // 5. Resumen de Ocupación por las 8 Zonas Cromáticas
        $zones = Zone::orderBy('display_order')
            ->with(['slips.boats' => function ($q) {
                $q->where('navigation_status', '!=', 'de_baja');
            }])
            ->get();

        return view('dashboard', compact(
            'totalBoats',
            'boatsInMarina',
            'boatsSailing',
            'totalSlips',
            'occupiedSlips',
            'freeSlips',
            'expiringDocuments',
            'activeSailings',
            'zones'
        ));
    }
}
