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
        Schema::create('modification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->time('modified_check_in')->nullable();
            $table->time('modified_check_out')->nullable();
            $table->json('modified_breaks')->nullable();
            $table->text('modified_note')->nullable();
            $table->enum('status', ['pending', 'approval', 'rejected'])->default('pending');
            $table->foreignId('modified_approval_by')->nullable()->constrained('users');
            $table->timestamp('modified_approval_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modification_requests');
    }
};
