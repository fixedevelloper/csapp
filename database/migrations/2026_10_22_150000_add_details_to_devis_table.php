<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            // Réponses propres au service demandé (questions du formulaire en étapes)
            $table->json('details')->nullable()->after('budget');
            $table->string('deadline', 100)->nullable()->after('budget');
            // Page d'où vient la demande (ex. /services/creation-site-web)
            $table->string('source', 150)->nullable()->after('deadline');
            // Facultative : les questions fermées suffisent pour les services listés
            $table->text('description')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropColumn(['details', 'deadline', 'source']);
            $table->text('description')->nullable(false)->change();
        });
    }
};
