<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            // Guardamos los public_id de los archivos que ya fueron vistos
            $table->json('archivos_vistos')->nullable()->default(null);
        });
    }

    public function down()
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            $table->dropColumn('archivos_vistos');
        });
    }
};