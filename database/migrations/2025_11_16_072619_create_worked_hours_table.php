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
        if (!Schema::hasTable('worked_hours')) {
            Schema::create('worked_hours', function (Blueprint $table) {
                $table->id();
                $table->text('task');
                $table->integer('hours')->default(0);
                $table->integer('minutes')->default(0);
                $table->date('date')->default(now());
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worked_hours');
    }
};
