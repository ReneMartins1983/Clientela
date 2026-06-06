<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('note'); // call | email | meeting | whatsapp | note
            $table->text('notes');
            $table->timestamp('happened_at');
            $table->timestamps();

            $table->index(['client_id', 'happened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
