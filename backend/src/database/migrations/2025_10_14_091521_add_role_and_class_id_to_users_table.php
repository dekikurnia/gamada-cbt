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
        Schema::table('users', function (Blueprint $table) {
            // kolom role untuk menentukan peran user
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('password');

            // relasi ke tabel classes
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn(['role', 'class_id']);
        });
    }
};
