<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('nombres');
            $table->string('apellidos');
            $table->enum('tipo', ['Trabajo', 'Personal', 'Proveedores', 'Otros']);
            $table->string('direccion');
            $table->string('telefono', 20);
            $table->string('sitio_web')->nullable();
            $table->string('empresa')->nullable();
            $table->string('cargo')->nullable();
            $table->tinyInteger('eliminado')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contactos');
    }
};
