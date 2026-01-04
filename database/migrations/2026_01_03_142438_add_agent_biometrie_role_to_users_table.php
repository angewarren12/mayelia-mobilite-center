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
        // Modifier l'enum pour ajouter 'agent_biometrie'
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'agent', 'oneci', 'agent_biometrie') DEFAULT 'agent'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'ancienne définition
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'agent', 'oneci') DEFAULT 'agent'");
    }
};
