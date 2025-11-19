<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Quién lo envió (cliente)
            $table->string('codigo_envio')->unique();  // Código único para diferenciar (ej: COMP-0001)
            $table->enum('tipo', ['pago', 'retencion']);  // Switch
            $table->string('url_archivo');  // URL de Cloudinary
            $table->date('fecha_envio');  // Fecha de envío
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};