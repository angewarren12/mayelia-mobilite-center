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
        Schema::table('dossier_ouvert', function (Blueprint $table) {
            $table->boolean('informations_client_verifiees')->default(false)->after('documents_manquants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dossier_ouvert', function (Blueprint $table) {
            $table->dropColumn('informations_client_verifiees');
        });
    }
};
