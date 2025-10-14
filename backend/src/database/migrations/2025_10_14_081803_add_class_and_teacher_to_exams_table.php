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
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete()->after('duration');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete()->after('class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['class_id', 'teacher_id']);
        });
    }
};
