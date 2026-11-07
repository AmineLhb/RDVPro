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
        Schema::create('record_field_templates', function (Blueprint $table) {
            $table->id();
            $table->string('business_type');
            $table->json('field_definitions'); // JSON list of fields: [{"name": "...", "label": "...", "type": "text/long_text", "required": true/false}]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_field_templates');
    }
};
