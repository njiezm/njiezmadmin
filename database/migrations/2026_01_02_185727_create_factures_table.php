<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('reference');
            $table->date('date');
            $table->date('deadline');
            $table->decimal('tva_rate', 5, 2);
            $table->decimal('total_ht', 10, 2);
            $table->decimal('total_tva', 10, 2);
            $table->decimal('total_ttc', 10, 2);
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};