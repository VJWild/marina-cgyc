<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\View\View;

class PublicMarinaController extends Controller
{
    public function index(): View
    {
        // Privacidad estricta a nivel de Backend: no se seleccionan matricula, owner_name, eslora_ft ni aforo_ft
        $zones = Zone::orderBy('display_order')
            ->with(['slips' => function ($query) {
                $query->select('id', 'code', 'zone_id', 'sort_order', 'status')
                    ->orderBy('sort_order')
                    ->with(['boats' => function ($boatQuery) {
                        $boatQuery->select('id', 'slip_id', 'name', 'navigation_status')
                            ->where('navigation_status', '!=', 'de_baja');
                    }]);
            }])
            ->get(['id', 'name', 'color_key', 'hex_code', 'display_order']);

        return view('welcome', compact('zones'));
    }
}
