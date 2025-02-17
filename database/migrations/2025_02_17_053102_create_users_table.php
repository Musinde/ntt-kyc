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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('id_no')->nullable();
            $table->string('gender')->nullable();
            $table->string('dob')->nullable();
            $table->integer('face_match')->default(0);
            $table->string('id_url')->nullable();
            $table->string('detected_face')->nullable();
            $table->string('selfie')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
