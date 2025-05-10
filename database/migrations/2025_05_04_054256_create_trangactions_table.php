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
        Schema::create('trangactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('preson_id')->nullable()->references('id')->on('accounting')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->unsignedInteger('qty')->nullable();
            $table->decimal('price', 10,2);
            $table->decimal('total', 10,2);
            $table->decimal('subTotal',10,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trangactions');
    }
};
