<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->timestamp('check_in_validated_at')->nullable()->after('check_in');
            $table->foreignId('check_in_validated_by')->nullable()->after('check_in_validated_at')->constrained('users')->noActionOnDelete();
            $table->timestamp('check_out_validated_at')->nullable()->after('check_out');
            $table->foreignId('check_out_validated_by')->nullable()->after('check_out_validated_at')->constrained('users')->noActionOnDelete();
            $table->timestamp('absence_validated_at')->nullable()->after('remarks');
            $table->foreignId('absence_validated_by')->nullable()->after('absence_validated_at')->constrained('users')->noActionOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['check_in_validated_by']);
            $table->dropForeign(['check_out_validated_by']);
            $table->dropForeign(['absence_validated_by']);
            $table->dropColumn([
                'check_in_validated_at',
                'check_in_validated_by',
                'check_out_validated_at',
                'check_out_validated_by',
                'absence_validated_at',
                'absence_validated_by',
            ]);
        });
    }
};
