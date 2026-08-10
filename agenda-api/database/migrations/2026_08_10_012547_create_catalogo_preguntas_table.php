<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalogo_preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('pregunta');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalogo_preguntas');
    }
};
