<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nom')->nullable()->after('id');
            $table->string('prenom')->nullable()->after('nom');
            $table->string('role')->default('agent')->after('prenom');
            $table->boolean('actif')->default(true)->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nom', 'prenom', 'role', 'actif']);
        });
    }
};


