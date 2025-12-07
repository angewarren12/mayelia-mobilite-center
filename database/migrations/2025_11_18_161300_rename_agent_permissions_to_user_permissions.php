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
        if (Schema::hasTable('agent_permissions')) {
            // Migrer les données vers user_permissions
            DB::statement('
                CREATE TABLE IF NOT EXISTS user_permissions (
                    user_id BIGINT UNSIGNED NOT NULL,
                    permission_id BIGINT UNSIGNED NOT NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    PRIMARY KEY (user_id, permission_id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
                )
            ');
            
            // Migrer les données : trouver les users correspondants aux agents
            $agentPermissions = DB::table('agent_permissions')->get();
            
            foreach ($agentPermissions as $ap) {
                // Trouver l'agent
                $agent = DB::table('agents')->where('id', $ap->agent_id)->first();
                
                if ($agent) {
                    // Trouver le user correspondant par email
                    $user = DB::table('users')->where('email', $agent->email)->where('role', 'agent')->first();
                    
                    if ($user) {
                        // Insérer dans user_permissions
                        DB::table('user_permissions')->insertOrIgnore([
                            'user_id' => $user->id,
                            'permission_id' => $ap->permission_id,
                            'created_at' => $ap->created_at,
                            'updated_at' => $ap->updated_at,
                        ]);
                    }
                }
            }
            
            // Supprimer l'ancienne table
            Schema::dropIfExists('agent_permissions');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer agent_permissions si nécessaire
        if (Schema::hasTable('user_permissions') && Schema::hasTable('agents')) {
            Schema::create('agent_permissions', function (Blueprint $table) {
                $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
                $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
                $table->timestamps();
                $table->primary(['agent_id', 'permission_id']);
            });
            
            // Migrer les données en sens inverse
            $userPermissions = DB::table('user_permissions')
                ->join('users', 'user_permissions.user_id', '=', 'users.id')
                ->where('users.role', 'agent')
                ->get();
            
            foreach ($userPermissions as $up) {
                $agent = DB::table('agents')->where('email', $up->email)->first();
                if ($agent) {
                    DB::table('agent_permissions')->insertOrIgnore([
                        'agent_id' => $agent->id,
                        'permission_id' => $up->permission_id,
                        'created_at' => $up->created_at,
                        'updated_at' => $up->updated_at,
                    ]);
                }
            }
            
            Schema::dropIfExists('user_permissions');
        }
    }
};
