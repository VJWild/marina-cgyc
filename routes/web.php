<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicMarinaController;
use App\Livewire\BoatsInventory;
use App\Livewire\SlipsBoard;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Vista Pública de Invitados (Datos protegidos)
Route::get('/', [PublicMarinaController::class, 'index'])->name('welcome');

// Rutas Protegidas de Capitanía
Route::middleware(['auth'])->group(function () {

    // Home / Dashboard con KPIs en vivo, alertas de documentos y mapa
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Control de Muelles (Secciones, Asignación, Zarpes y Retornos)
    Route::get('/muelles', SlipsBoard::class)->name('slips.index');

    // Directorio de Embarcaciones (CRUD, Socio, Documentos y Bajas)
    Route::get('/embarcaciones', BoatsInventory::class)->name('boats.index');

    // Plano General con Resumen por Zona
    Route::get('/mapa', function () {
        $zones = Zone::orderBy('display_order')->with('slips.boats')->get();
        return view('map', compact('zones'));
    })->name('map.index');

    Route::view('/profile', 'profile')->name('profile');

    // Logout directo desde la Sidebar
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome');
    })->name('logout');
});

require __DIR__.'/auth.php';
