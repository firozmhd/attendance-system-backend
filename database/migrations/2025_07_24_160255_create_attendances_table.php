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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // Foreign key referencing the 'users' table (student ID).
            // 'cascade' means if a user is deleted, their attendance records are also deleted.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // The date for which the attendance is recorded.
            $table->date('date');
            // The status of attendance (e.g., 'present', 'absent', 'leave').
            $table->string('status')->default('absent');
            $table->timestamps();

            // Ensures that a student can only have one attendance record per day.
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
