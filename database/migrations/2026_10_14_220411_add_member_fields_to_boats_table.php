<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->boolean('has_member')->default(false)->after('owner_name');
            $table->string('member_action_number', 30)->nullable()->index()->after('has_member'); // Ej: P-040
            $table->string('member_name', 180)->nullable()->after('member_action_number');
            $table->string('member_dni', 30)->nullable()->after('member_name');
            $table->string('member_phone', 40)->nullable()->after('member_dni');
            $table->string('member_email', 150)->nullable()->after('member_phone');
        });
    }

    public function down(): void
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->dropColumn([
                'has_member',
                'member_action_number',
                'member_name',
                'member_dni',
                'member_phone',
                'member_email',
            ]);
        });
    }
};
