<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('subject');
            $table->text('message');

            // Optionnel : IP & User-Agent (audit / sécurité)
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // Statut du message
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
        Schema::create('devis', function (Blueprint $table) {
            $table->id();

            // Informations client
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->string('company')->nullable();

            // Détails du projet
            $table->string('project_type');
            $table->string('budget')->nullable();
            $table->text('description');

            // Statut
            $table->enum('status', ['nouveau', 'en_cours', 'envoye', 'accepte'])
                ->default('nouveau');

            // Infos anti-spam / audit
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('devis');
    }
};
