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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->noActionOnDelete();
            $table->foreignId('company_id')->constrained('companies')->noActionOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->noActionOnDelete();
            $table->foreignId('enrolled_by')->constrained('users')->noActionOnDelete();
            $table->string('nss_number');
            $table->string('status')->default('pending_forms');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('endorsement_date')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
