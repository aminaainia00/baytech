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
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('country');
            $table->string('city');
            $table->string('category');
            $table->string('title');
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('livingrooms');
            $table->double('area');
            $table->double('price');
            $table->string('mainImage');
            $table->text('descreption')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
