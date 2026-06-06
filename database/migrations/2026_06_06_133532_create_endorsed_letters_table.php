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
        Schema::create('endorsed_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->noActionOnDelete();
            $table->foreignId('letter_template_id')->nullable()->constrained('letter_templates')->noActionOnDelete();
            $table->foreignId('endorsed_by')->constrained('users')->noActionOnDelete();
            $table->string('generated_file_path');
            $table->string('status')->default('pending');
            $table->string('validated_file_path')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endorsed_letters');
    }
};
