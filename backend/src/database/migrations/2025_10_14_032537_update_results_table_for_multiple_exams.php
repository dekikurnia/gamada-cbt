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
        Schema::table('results', function (Blueprint $table) {
            // Ubah kolom score menjadi nullable
            $table->integer('score')->nullable()->change();

            // Tambahkan kombinasi unik exam_id + user_id
            $table->unique(['exam_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropUnique(['exam_id', 'user_id']);
            $table->integer('score')->nullable(false)->change();
        });
    }
};
