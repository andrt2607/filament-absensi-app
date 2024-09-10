<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('latitude', 'start_latitude');
            $table->renameColumn('longitude', 'start_longitude');
            $table->string('end_latitude')->nullable();
            $table->string('end_longitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('start_latitude', 'latitude');
            $table->renameColumn('start_longitude', 'longitude');
            $table->dropColumn('end_latitude');
            $table->dropColumn('end_longitude');
        });
    }
};
