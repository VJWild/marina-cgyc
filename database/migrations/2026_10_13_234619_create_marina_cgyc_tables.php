<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 2. ZONES (Zonas del Mapa)
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('color_key', 20);
            $table->string('hex_code', 10);
            $table->integer('display_order');
            $table->timestamps();
        });

        // 3. SLIPS (Puestos / Muelles)
        Schema::create('slips', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // Ej: 1..104, 79A
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->integer('sort_order');
            $table->integer('max_aforo_ft')->nullable();
            $table->string('status', 20)->default('free'); // free | occupied | maintenance
            $table->timestamps();
        });

// 4. BOATS (Embarcaciones)
        Schema::create('boats', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->index();
            $table->string('matricula', 60)->index();
            $table->string('owner_name', 180); // Propietario / Armador
            $table->decimal('eslora_ft', 6, 2)->nullable(); // Eslora
            $table->decimal('arqueo_bruto', 6, 2)->nullable(); // Arqueo Bruto (AB / GT)
            $table->integer('crew_capacity')->nullable(); // Capacidad de tripulación / personas
            $table->decimal('aforo_ft', 6, 2)->nullable(); // Aforo de muelle en pies
            $table->foreignId('slip_id')->nullable()->constrained('slips')->nullOnDelete();
            $table->string('navigation_status', 20)->default('en_marina'); // en_marina | de_viaje | de_baja
            $table->text('deactivated_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 5. BOAT_DOCUMENTS (Licencia de Navegación y Certificado de Radio)
        Schema::create('boat_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boat_id')->constrained('boats')->cascadeOnDelete();
            $table->string('document_type', 80); // Licencia de Navegación | Certificado de Radio
            $table->string('document_number', 80); // Ej: INEA/CAGSI/2026/01-A-777
            $table->date('issued_at')->nullable(); // Fecha de expedición
            $table->date('expires_at')->index(); // Fecha de vencimiento
            $table->timestamps();
        });

        // 6. SAILINGS (Bitácora de Zarpes y Retornos)
        Schema::create('sailings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boat_id')->constrained('boats')->cascadeOnDelete();
            $table->foreignId('origin_slip_id')->nullable()->constrained('slips')->nullOnDelete();
            $table->foreignId('return_slip_id')->nullable()->constrained('slips')->nullOnDelete();
            $table->string('destination', 180);
            $table->timestamp('departed_at');
            $table->timestamp('expected_return_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sailings');
        Schema::dropIfExists('boat_documents');
        Schema::dropIfExists('boats');
        Schema::dropIfExists('slips');
        Schema::dropIfExists('zones');
    }
};
