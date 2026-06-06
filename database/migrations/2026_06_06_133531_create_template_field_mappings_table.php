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
        Schema::create('template_field_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_template_id')->constrained('letter_templates')->noActionOnDelete();
            $table->string('field_key');
            $table->string('field_type')->default('text');
            $table->string('label');
            $table->integer('page_number')->default(1);
            $table->float('x', 10, 2);
            $table->float('y', 10, 2);
            $table->float('width', 10, 2)->nullable();
            $table->float('height', 10, 2)->nullable();
            $table->integer('font_size')->nullable()->default(12);
            $table->string('font_family')->nullable();
            $table->string('text_alignment')->nullable()->default('left');
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_field_mappings');
    }
};
