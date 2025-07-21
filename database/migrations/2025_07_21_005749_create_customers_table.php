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
        Schema::create('customers', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('email')->unique(); // não pode repetir
        $table->string('telefone');
        $table->date('data_nascimento');
        $table->string('endereco');
        $table->string('complemento')->nullable();
        $table->string('bairro');
        $table->string('cep');
        $table->timestamps(); // created_at e updated_at
        $table->softDeletes(); // para soft delete
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
