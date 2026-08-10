<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('preguntas_recuperacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('catalogo_pregunta_id')->constrained('catalogo_preguntas')->onDelete('cascade');
            $table->string('respuesta');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('preguntas_recuperacion');
    }
};
