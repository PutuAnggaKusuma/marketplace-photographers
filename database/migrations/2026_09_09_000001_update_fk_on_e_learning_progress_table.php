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
        Schema::table('e_learning_progress', function (Blueprint $table) {
            // Drop old foreign key pointing to e_learning_materials if exists
            $table->dropForeign('e_learning_progress_id_e_learning');
            
            // Add new foreign key referencing the active elearnings table
            $table->foreign('id_e_learning', 'e_learning_progress_id_e_learning')
                  ->references('id')
                  ->on('elearnings')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('e_learning_progress', function (Blueprint $table) {
            $table->dropForeign('e_learning_progress_id_e_learning');
            $table->foreign('id_e_learning', 'e_learning_progress_id_e_learning')
                  ->references('id')
                  ->on('e_learning_materials')
                  ->onDelete('cascade');
        });
    }
};
