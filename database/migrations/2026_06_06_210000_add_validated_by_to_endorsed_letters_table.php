<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('endorsed_letters', function (Blueprint $table) {
            $table->foreignId('validated_by')->nullable()->constrained('users')->noActionOnDelete()->after('validated_at');
        });
    }

    public function down(): void
    {
        Schema::table('endorsed_letters', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn('validated_by');
        });
    }
};
