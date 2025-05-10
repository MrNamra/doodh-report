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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trangaction_id')->references('id')->on('trangactions')->cascadeOnDelete();
            $table->foreignId('preson_id')->references('id')->on('accounting')->cascadeOnDelete();
            $table->enum('trangcation',['credit','debit']);
            $table->decimal('ammount', 8,2);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
