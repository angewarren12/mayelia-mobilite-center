<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'enum pour ajouter 'oneci'
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'agent', 'oneci') DEFAULT 'agent'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retirer 'oneci' de l'enum (attention: les utilisateurs avec role='oneci' devront être modifiés avant)
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'agent') DEFAULT 'agent'");
    }
};
