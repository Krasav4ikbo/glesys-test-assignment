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
        Schema::create('heartbeats', function (Blueprint $table) {
            $table->string('applicationKey');
            $table->string('heartbeatKey');
            $table->integer('unhealthyAfterMinutes');
            $table->primary(['applicationKey', 'heartbeatKey']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heartbeats');
    }
};
